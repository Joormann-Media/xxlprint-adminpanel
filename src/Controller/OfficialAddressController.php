<?php

namespace App\Controller;

use App\Entity\OfficialAddress;
use App\Form\OfficialAddressType;
use App\Repository\OfficialAddressRepository;
use App\Form\OfficialAddressImportType;
use App\Service\AddressImportService;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;
use App\Repository\CityPostalcodeRepository;



#[Route('/official-address')]
final class OfficialAddressController extends AbstractController
{
    #[Route(name: 'app_official_address_index', methods: ['GET'])]
public function index(Request $request, OfficialAddressRepository $repo): Response
{
    $search = $request->query->get('search', '');
    $limit = (int) $request->query->get('limit', 50);
    $page = max(1, (int) $request->query->get('page', 1));
    $sort = $request->query->get('sort', 'postcode');
    $direction = strtoupper($request->query->get('direction', 'ASC'));

    $queryBuilder = $repo->createQueryBuilder('o');

   if ($search) {
    $normalized = \App\Entity\OfficialAddress::buildNormalized($search, '', '', '');
    $queryBuilder
        ->where('o.normalized LIKE :needle')
        ->setParameter('needle', '%' . $normalized . '%');
}


    // Whitelist für erlaubte Sortierfelder
    $allowedSortFields = ['postcode', 'city', 'district', 'subdistrict', 'neighbourhood', 'street', 'houseNumber'];
    if (!in_array($sort, $allowedSortFields, true)) {
        $sort = 'postcode';
    }
    if (!in_array($direction, ['ASC', 'DESC'], true)) {
        $direction = 'ASC';
    }

    $queryBuilder->orderBy('o.' . $sort, $direction);

    $pager = new Pagerfanta(new QueryAdapter($queryBuilder));
    $pager->setMaxPerPage($limit);
    $pager->setCurrentPage($page);

    $template = $request->isXmlHttpRequest()
        ? 'official_address/__table.html.twig'
        : 'official_address/index.html.twig';

    return $this->render($template, [
        'pagination' => $pager,
        'search' => $search,
        'limit' => $limit,
        'route' => $request->attributes->get('_route'),
        'query' => $request->query->all(),
        'page_title' => 'Offizielle Adressen',
    ]);
}


    #[Route('/new', name: 'app_official_address_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $officialAddress = new OfficialAddress();
        $form = $this->createForm(OfficialAddressType::class, $officialAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($officialAddress);
            $entityManager->flush();

            return $this->redirectToRoute('app_official_address_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('official_address/new.html.twig', [
            'official_address' => $officialAddress,
            'form' => $form,
            'page_title' => 'Neue offizielle Adresse',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_official_address_show', methods: ['GET'])]
    public function show(OfficialAddress $officialAddress): Response
    {
        return $this->render('official_address/show.html.twig', [
            'official_address' => $officialAddress,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_official_address_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OfficialAddress $officialAddress, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OfficialAddressType::class, $officialAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_official_address_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('official_address/edit.html.twig', [
            'official_address' => $officialAddress,
            'form' => $form,
            'page_title' => 'Offizielle Adresse bearbeiten',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_official_address_delete', methods: ['POST'])]
    public function delete(Request $request, OfficialAddress $officialAddress, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $officialAddress->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($officialAddress);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_official_address_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/import', name: 'app_official_address_import', methods: ['GET', 'POST'])]
    public function import(
        Request $request,
        AddressImportService $importService,
        EntityManagerInterface $em,
        OfficialAddressRepository $repo
    ): Response {
        $form = $this->createForm(OfficialAddressImportType::class);
        $form->handleRequest($request);

        $start = $form->get('postalcode_start')->getData();
        $city = $form->get('cityQuery')->getData();
        $postcodes = [];
        $streetLetters = [];

        if ($form->isSubmitted() && $form->isValid()) {
            if ($city) {
                $results = $repo->createQueryBuilder('a')
                    ->select('DISTINCT a.postcode')
                    ->where('LOWER(a.city) LIKE :city')
                    ->setParameter('city', mb_strtolower($city) . '%')
                    ->orderBy('a.postcode', 'ASC')
                    ->getQuery()
                    ->getScalarResult();
                $postcodes = array_column($results, 'postcode');

                if (empty($postcodes)) {
                    $liveAdressen = $importService->fetchFromOverpass((int)$start);
                    foreach ($liveAdressen as $addr) {
                        $postcodes[] = $addr->getPostcode();
                    }
                }
            } elseif ($start) {
                $postcodes = [$start];
            }

            foreach ($postcodes as $plz) {
                $letters = $repo->createQueryBuilder('a')
                    ->select('SUBSTRING(a.street, 1, 1) AS letter')
                    ->where('a.postcode = :plz')
                    ->setParameter('plz', $plz)
                    ->groupBy('letter')
                    ->orderBy('letter', 'ASC')
                    ->getQuery()
                    ->getSingleColumnResult();

                // Fix: Wenn keine Buchstaben in DB, hole sie per Overpass
                if (empty($letters)) {
                    $addresses = $importService->fetchFromOverpass((int)$plz);
                    foreach ($addresses as $addr) {
                        $firstLetter = strtoupper(substr($addr->getStreet(), 0, 1));
                        $streetLetters[$plz][] = $firstLetter;
                    }
                    $streetLetters[$plz] = array_values(array_unique($streetLetters[$plz] ?? []));
                } else {
                    $streetLetters[$plz] = $letters;
                }
            }
        }

        return $this->render('official_address/import.html.twig', [
            'form' => $form,
            'postcodes' => $postcodes,
            'streetLetters' => $streetLetters,
            'page_title' => 'Offizielle Adressen importieren',
        ]);
    }

    #[Route('/import/ajax', name: 'app_official_address_import_ajax', methods: ['POST'])]
    public function importAjax(
        Request $request,
        AddressImportService $importService,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
$plz = $data['plz'] ?? null;

        $letter = $request->request->get('letter');

        $addresses = $importService->fetchFromOverpassByStreetLetter($plz, $letter);
        $result = $importService->persist($addresses, $em);

        return new JsonResponse([
            'inserted' => $result['inserted'],
            'skipped' => $result['skipped'],
            'plz' => $plz,
            'letter' => $letter,
            'errors' => $result['errors'] ?? [],  // <-- optionales Fehler-Array
        ]);
    }

    #[Route('/api/postalcodes/by-city', name: 'api_postalcodes_by_city', methods: ['GET'])]
    public function byCity(Request $request, OfficialAddressRepository $repo): JsonResponse
    {
        $city = $request->query->get('q', '');

        if (strlen($city) < 2) {
            return new JsonResponse([]);
        }

        $existingCities = $repo->createQueryBuilder('a')
            ->select('DISTINCT a.city')
            ->orderBy('a.city', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();

        $results = $repo->createQueryBuilder('a')
            ->select('DISTINCT a.postcode')
            ->where('LOWER(a.city) LIKE :city')
            ->setParameter('city', mb_strtolower($city) . '%')
            ->orderBy('a.postcode', 'ASC')
            ->getQuery()
            ->getScalarResult();

        $postcodes = array_column($results, 'postcode');

        $bestMatch = null;
        if (empty($postcodes)) {
            foreach ($existingCities as $candidate) {
                similar_text(strtolower($city), strtolower($candidate), $score);
                if ($score > 80) {
                    $bestMatch = $candidate;
                    break;
                }
            }
        }

        return new JsonResponse([
            'postcodes' => $postcodes,
            'suggestion' => $bestMatch,
        ]);
    }

    #[Route('/api/geodata/cities-by-plz-prefix/{prefix}', name: 'api_plz_cities')]
    public function getCitiesByPlzPrefix(string $prefix, OfficialAddressRepository $repo): JsonResponse
    {
        $cities = $repo->createQueryBuilder('a')
            ->select('DISTINCT a.city')
            ->where('a.postcode LIKE :prefix')
            ->setParameter('prefix', $prefix . '%')
            ->orderBy('a.city', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();

        return $this->json($cities);
    }
    #[Route('/api/geocode/city', name: 'api_geocode_city', methods: ['GET'])]
public function apiGeocodeCity(Request $request): JsonResponse
{
    $query = $request->query->get('q');
    if (!$query || strlen($query) < 2) {
        return new JsonResponse([], Response::HTTP_BAD_REQUEST);
    }

    $client = HttpClient::create();
    $response = $client->request('GET', 'https://nominatim.openstreetmap.org/search', [
        'query' => [
            'q' => $query,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 5,
        ],
        'headers' => [
            'User-Agent' => 'Tekath-ImportTool/1.0 (+https://joormann-media.de)',
        ],
    ]);

    $data = $response->toArray();

    // Formatieren
    $results = array_map(function ($entry) {
        return [
            'name' => $entry['display_name'],
            'lat' => $entry['lat'],
            'lon' => $entry['lon'],
            'display_name' => $entry['display_name'],
            'address' => $entry['address'] ?? [],
        ];
    }, $data);

    return $this->json($results);
}

#[Route('/api/geodata/postcodes', name: 'api_geodata_postcodes_by_city', methods: ['GET'])]
public function getPostcodesByCity(Request $request, OfficialAddressRepository $repo): JsonResponse
{
    $city = $request->query->get('city');
    if (!$city || strlen($city) < 2) {
        return new JsonResponse([]);
    }

    $results = $repo->createQueryBuilder('a')
        ->select('DISTINCT a.postcode')
        ->where('LOWER(a.city) = :city')
        ->setParameter('city', mb_strtolower($city))
        ->orderBy('a.postcode', 'ASC')
        ->getQuery()
        ->getScalarResult();

    return new JsonResponse(array_column($results, 'postcode'));
}
#[Route('/api/geocode/plz-by-city', name: 'api_geocode_plz_by_city', methods: ['GET'])]
public function apiGeocodePlzByCity(Request $request): JsonResponse
{
    $city = $request->query->get('q');
    if (!$city || strlen($city) < 2) {
        return new JsonResponse([], Response::HTTP_BAD_REQUEST);
    }

    $client = HttpClient::create();
    $response = $client->request('GET', 'https://nominatim.openstreetmap.org/search', [
        'query' => [
            'city' => $city,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 50,
        ],
        'headers' => [
            'User-Agent' => 'Tekath-Importer/1.0',
        ],
    ]);

    $data = $response->toArray();

    $postcodes = [];
    foreach ($data as $entry) {
        if (isset($entry['address']['postcode'])) {
            $postcodes[] = $entry['address']['postcode'];
        }
    }

    $postcodes = array_unique($postcodes);
    sort($postcodes);

    return $this->json($postcodes);
}
#[Route('/api/plz/by-city', name: 'api_plz_by_city', methods: ['GET'])]
public function getPlzByCity(Request $request, CityPostalcodeRepository $repo): JsonResponse
{
    $city = $request->query->get('q');
    $results = $repo->createQueryBuilder('c')
        ->select('DISTINCT c.postcode')
        ->where('LOWER(c.city) = :city')
        ->setParameter('city', mb_strtolower($city))
        ->orderBy('c.postcode', 'ASC')
        ->getQuery()
        ->getSingleColumnResult();

    return $this->json($results);
}

#[Route('/api/local-city-suggest', name: 'api_local_city_suggest', methods: ['GET'])]
public function suggestCities(Request $request, CityPostalcodeRepository $repo): JsonResponse
{
    $q = trim($request->query->get('q', ''));
    if (strlen($q) < 2) {
        return new JsonResponse([]);
    }

    $results = $repo->createQueryBuilder('c')
        ->select('DISTINCT c.city')
        ->where('LOWER(c.city) LIKE :q')
        ->setParameter('q', mb_strtolower($q) . '%')
        ->orderBy('c.city', 'ASC')
        ->setMaxResults(5)
        ->getQuery()
        ->getSingleColumnResult();

    return $this->json($results);
}

#[Route('/api/local-city-postcodes', name: 'api_local_city_postcodes', methods: ['GET'])]
public function getPostcodesForCity(Request $request, CityPostalcodeRepository $repo): JsonResponse
{
    $city = trim($request->query->get('q', ''));
    if (!$city || strlen($city) < 2) {
        return new JsonResponse([]);
    }

    $postcodes = $repo->createQueryBuilder('c')
        ->select('DISTINCT c.postcode')
        ->where('LOWER(c.city) = :city')
        ->setParameter('city', mb_strtolower($city))
        ->orderBy('c.postcode', 'ASC')
        ->getQuery()
        ->getSingleColumnResult();

    return $this->json($postcodes);
}
#[Route('/api/import/by-plz', name: 'api_address_import_by_plz', methods: ['POST'])]
public function importByPlz(Request $request, AddressImportService $importService, EntityManagerInterface $em): JsonResponse
{
    $plz = $request->request->get('plz');

    // Unterstützung für JSON
    if (!$plz) {
        $json = json_decode($request->getContent(), true);
        $plz = $json['plz'] ?? null;
    }

    if (!$plz || !preg_match('/^\d{5}$/', $plz)) {
        return new JsonResponse(['error' => 'Ungültige PLZ'], 400);
    }

    $addresses = $importService->fetchFromOverpass($plz);
    $result = $importService->persist($addresses, $em);

    return $this->json([
        'status' => 'ok',
        'inserted' => $result['inserted'],
        'skipped' => $result['skipped'],
        'total' => count($addresses),
        'plz' => $plz,
    ]);
}
#[Route('/api/import/by-street-letter', name: 'app_official_address_import_ajax', methods: ['POST'])]
public function importByStreetLetter(Request $request, AddressImportService $importService, EntityManagerInterface $em): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $plz = $data['plz'] ?? null;
    $letter = $data['letter'] ?? null;

    if (!$plz || !$letter) {
        return $this->json(['error' => 'Fehlende Parameter'], 400);
    }

    $results = $importService->fetchFromOverpassByStreetLetter($plz, $letter);
    $stats = $importService->persist($results, $em);

    return $this->json([
        'plz' => $plz,
        'letter' => $letter,
        'inserted' => $stats['inserted'],
        'skipped' => $stats['skipped'],
        'errors' => $stats['errors'] ?? [],
    ]);
}
// src/Controller/OfficialAddressController.php

#[Route('/api/address/suggest', name: 'api_address_suggest', methods: ['GET'])]
public function addressSuggest(Request $request, OfficialAddressRepository $repo): JsonResponse
{
    $id = $request->query->get('id');
    if ($id) {
        $address = $repo->find($id);
        if ($address) {
            return $this->json(['results' => [[
                'id' => $address->getId(),
                'text' => (string)$address,
                // Optional: weitere Felder (für Template)
            ]]]);
        }
        // Not found: leere Liste zurück
        return $this->json(['results' => []]);
    }

    $q = trim($request->query->get('q', ''));
    if (strlen($q) < 3) return new JsonResponse(['results' => []]);

    $parts = preg_split('/[\s,]+/', $q, -1, PREG_SPLIT_NO_EMPTY);

    $qb = $repo->createQueryBuilder('a');
    $andX = $qb->expr()->andX();

    foreach ($parts as $i => $part) {
        $orX = $qb->expr()->orX();
        $orX->add($qb->expr()->like('LOWER(a.street)', ':p' . $i));
        $orX->add($qb->expr()->like('LOWER(a.houseNumber)', ':p' . $i));
        $orX->add($qb->expr()->like('LOWER(a.postcode)', ':p' . $i));
        $orX->add($qb->expr()->like('LOWER(a.city)', ':p' . $i));
        $orX->add($qb->expr()->like('LOWER(a.district)', ':p' . $i));
        $andX->add($orX);
        $qb->setParameter('p' . $i, '%' . strtolower($part) . '%');
    }

    $qb->where($andX)
       ->orderBy('a.city', 'ASC')
       ->addOrderBy('a.street', 'ASC')
       ->setMaxResults(30);

    $results = $qb->getQuery()->getResult();

    $formatted = array_map(fn($a) => [
        'id'   => $a->getId(),
        'text' => (string)$a,
        'street' => $a->getStreet(),
        'houseNumber' => $a->getHouseNumber(),
        'postcode' => $a->getPostcode(),
        'city' => $a->getCity(),
        'district' => $a->getDistrict(),
    ], $results);

    return $this->json(['results' => $formatted]);
}








}
