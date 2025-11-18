<?php

namespace App\Controller;

use App\Entity\Schoolkids;
use App\Form\SchoolkidsType;
use App\Repository\SchoolkidsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\School;
use App\Service\AddressCorrectionService;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

#[Route('/schoolkids')]
final class SchoolkidsController extends AbstractController
{
     #[Route(name: 'app_schoolkids_index', methods: ['GET'])]
public function index(Request $request, SchoolkidsRepository $schoolkidsRepository): Response
{
    $search = $request->query->get('search', '');
    $sort = $request->query->get('sort', 'lastName');
    $direction = strtoupper($request->query->get('direction', 'ASC'));
    $limit = (int)$request->query->get('limit', 50);
    $page = max(1, (int)$request->query->get('page', 1));

    // Whitelist der erlaubten Sortierfelder
    $allowedSort = [
        'lastName', 'firstName', 'city', 'zip',
        'school', // Achtung: Sonderfall, siehe unten!
        // weitere Felder nach Bedarf
    ];
    if (!in_array($sort, $allowedSort, true)) {
        $sort = 'lastName';
    }
    if (!in_array($direction, ['ASC', 'DESC'], true)) {
        $direction = 'ASC';
    }

    // Query bauen (inkl. Such- und Sortierlogik)
    $qb = $schoolkidsRepository->createQueryBuilder('k')
        ->leftJoin('k.school', 's')->addSelect('s');

    if ($search) {
        $needle = '%' . mb_strtolower($search) . '%';
        $qb->andWhere('LOWER(k.lastName) LIKE :needle OR LOWER(k.firstName) LIKE :needle OR LOWER(k.street) LIKE :needle OR LOWER(k.city) LIKE :needle OR LOWER(s.name) LIKE :needle OR LOWER(s.shorttag) LIKE :needle')
            ->setParameter('needle', $needle);
    }

    // Sortierung: Bei "school" nach s.name, sonst Feld von k
    if ($sort === 'school') {
        $qb->orderBy('s.name', $direction);
    } else {
        $qb->orderBy('k.' . $sort, $direction);
    }

    // Pagination mit Pagerfanta
    $pager = new Pagerfanta(new QueryAdapter($qb));
    $pager->setMaxPerPage($limit);
    $pager->setCurrentPage($page);

    // Template für AJAX oder Full-Page unterscheiden (optional)
    $template = $request->isXmlHttpRequest()
        ? 'schoolkids/_table.html.twig'
        : 'schoolkids/index.html.twig';

    return $this->render($template, [
        'pagination' => $pager,
        'schoolkids' => $pager->getCurrentPageResults(),
        'search' => $search,
        'sort' => $sort,
        'direction' => $direction,
        'limit' => $limit,
        'route' => $request->attributes->get('_route'),
        'query' => $request->query->all(),
        'page_title' => 'Schulkinder',
    ]);
}

    #[Route('/new', name: 'app_schoolkids_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schoolkid = new Schoolkids();
        $form = $this->createForm(SchoolkidsType::class, $schoolkid);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($schoolkid);
            $entityManager->flush();

            return $this->redirectToRoute('app_schoolkids_index');
        }

        return $this->render('schoolkids/new.html.twig', [
            'schoolkid' => $schoolkid,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_schoolkids_show', methods: ['GET'])]
    public function show(Schoolkids $schoolkid): Response
    {
        return $this->render('schoolkids/show.html.twig', [
            'schoolkid' => $schoolkid,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_schoolkids_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Schoolkids $schoolkid, EntityManagerInterface $entityManager): Response
    {
            $form = $this->createForm(SchoolkidsType::class, $schoolkid, [
        'address_text' => $schoolkid->getAddress() ? (string)$schoolkid->getAddress() : '',
    ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();

            return $this->redirectToRoute('app_schoolkids_index');
        }

        return $this->render('schoolkids/edit.html.twig', [
            'schoolkid' => $schoolkid,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_schoolkids_delete', methods: ['POST'])]
    public function delete(Request $request, Schoolkids $schoolkid, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolkid->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($schoolkid);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_schoolkids_index');
    }

    #[Route('/import', name: 'app_schoolkids_import', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Ungültiges JSON. Erwartet wird ein Array von Objekten.'], 400);
        }

        $imported = 0;
        $skipped = 0;
        $skippedDetails = [];

        foreach ($data as $index => $entry) {
            $errors = [];

            $schoolId = $entry['school_id'] ?? $entry['school'] ?? null;
            $school = $schoolId ? $em->getRepository(School::class)->find($schoolId) : null;
            if (!$school) {
                $errors[] = "Ungültige oder fehlende school_id";
            }

            $firstName = $entry['firstName'] ?? null;
            $lastName = $entry['lastName'] ?? null;
            if (!$firstName || !$lastName) {
                $errors[] = "Vor- oder Nachname fehlt";
            }

            $dob = null;
            if (!empty($entry['birthDate'])) {
                try {
                    $dob = new \DateTime($entry['birthDate']);
                } catch (\Exception) {
                    $errors[] = "Ungültiges Geburtsdatum";
                }
            }

            $requiredSeats = isset($entry['requiredSeats']) ? (int) $entry['requiredSeats'] : 1;
            if ($requiredSeats < 1 || $requiredSeats > 9) {
                $errors[] = "Unplausible Sitzplatzanzahl: $requiredSeats";
            }

            if ($errors) {
                $skipped++;
                $skippedDetails[] = [
                    'index' => $index,
                    'errors' => $errors,
                    'name' => ($firstName ?? '???') . ' ' . ($lastName ?? '???')
                ];
                continue;
            }

            $kid = new Schoolkids();
            $kid->setSchool($school);
            $kid->setFirstName($firstName);
            $kid->setLastName($lastName);
            $kid->setDateOfBirth($dob);
            $kid->setStreet($entry['street'] ?? '');
            $kid->setStreetNumber($entry['streetNumber'] ?? '');
            $kid->setZip($entry['zip'] ?? '');
            $kid->setCity($entry['city'] ?? '');
            $kid->setDistrict($entry['district'] ?? null);
            $kid->setContactEmail($entry['contactEmail'] ?? null);
            $kid->setContactPersonName($entry['contactPersonName'] ?? null);
            $kid->setContactPersonPhone($entry['contactPersonPhone'] ?? null);
            $kid->setKidPhone($entry['kidPhone'] ?? ($entry['phone'] ?? null));
            $kid->setSpecialInfos($entry['specialInfos'] ?? '');
            $kid->setNeedsAid((bool)($entry['needsAid'] ?? false));
            $kid->setAidType($entry['aidType'] ?? null);
            $kid->setHasCompanion((bool)($entry['hasCompanion'] ?? false));
            $kid->setCompanionName($entry['companionName'] ?? null);
            $kid->setRequiredSeats($requiredSeats);
            $kid->setLatitude($entry['latitude'] ?? null);
            $kid->setLongitude($entry['longitude'] ?? null);
            $kid->setActive(true);

            $em->persist($kid);
            $imported++;
        }

        $em->flush();

        return new JsonResponse([
            'imported' => $imported,
            'skipped' => $skipped,
            'skippedDetails' => $skippedDetails
        ]);
    }

    #[Route('/{id<\d+>}/correct-address', name: 'app_schoolkids_correct_address', methods: ['GET'])]
    public function correctAddress(
        Schoolkids $schoolkid,
        AddressCorrectionService $addressCorrectionService
    ): Response {
        $suggestion = $addressCorrectionService->suggestCorrection(
            $schoolkid->getZip(),
            $schoolkid->getStreet(),
            $schoolkid->getCity(),
            $schoolkid->getStreetNumber()
        );

        return $this->render('schoolkids/address_correction.html.twig', [
            'schoolkid' => $schoolkid,
            'suggestion' => $suggestion
        ]);
    }

    #[Route('/{id<\d+>}/apply-correction', name: 'app_schoolkids_apply_correction', methods: ['POST'])]
    public function applyCorrection(
        Request $request,
        Schoolkids $schoolkid,
        EntityManagerInterface $em
    ): Response {
        if (!$this->isCsrfTokenValid('correct' . $schoolkid->getId(), $request->get('csrf_token'))) {
            throw $this->createAccessDeniedException('CSRF-Token ungültig');
        }

        $schoolkid->setStreet($request->get('street'));
        $schoolkid->setStreetNumber($request->get('streetNumber'));
        $schoolkid->setZip($request->get('zip'));
        $schoolkid->setCity($request->get('city'));
        $schoolkid->setDistrict($request->get('district'));

        $em->flush();

        $this->addFlash('success', 'Adresse korrigiert 🎯');

        return $this->redirectToRoute('app_schoolkids_index');
    }

    #[Route('/mass-correct', name: 'app_schoolkids_mass_correction')]
public function massCorrect(
    SchoolkidsRepository $repo,
    AddressCorrectionService $correctionService
): Response {
    $students = $repo->findAll();
    $withSuggestions = [];

    foreach ($students as $kid) {
        $suggestion = $correctionService->suggestFor(
            $kid->getStreet(),
            $kid->getStreetNumber(),
            $kid->getZip(),
            $kid->getCity()
        );

        if ($suggestion) {
            $withSuggestions[] = [
                'kid' => $kid,
                'suggestion' => $suggestion
            ];
        }
    }

    return $this->render('schoolkids/mass_correction.html.twig', [
        'entries' => $withSuggestions
    ]);
}
#[Route('/apply-mass-correction', name: 'app_schoolkids_apply_mass_correction', methods: ['POST'])]
public function applyMassCorrection(
    Request $request,
    SchoolkidsRepository $repo,
    EntityManagerInterface $em
): Response {
    $token = $request->get('csrf_token');
    if (!$this->isCsrfTokenValid('mass_correction', $token)) {
        throw $this->createAccessDeniedException('Ungültiger CSRF-Token');
    }

    $applies = $request->get('apply', []);
    $data = $request->get('data', []);
    $count = 0;

    foreach ($applies as $id => $checked) {
        if (!isset($data[$id])) {
            continue;
        }

        $kid = $repo->find($id);
        if (!$kid) {
            continue;
        }

        $fields = $data[$id];

        $kid->setStreet($fields['street'] ?? $kid->getStreet());
        $kid->setStreetNumber($fields['streetNumber'] ?? $kid->getStreetNumber());
        $kid->setZip($fields['zip'] ?? $kid->getZip());
        $kid->setCity($fields['city'] ?? $kid->getCity());
        $kid->setDistrict($fields['district'] ?? $kid->getDistrict());

        $count++;
    }

    $em->flush();

    $this->addFlash('success', "$count Adressen erfolgreich aktualisiert 🎉");

    return $this->redirectToRoute('app_schoolkids_index');
}

#[Route('/auto-correct-all', name: 'app_schoolkids_autocorrect_all')]
public function autoCorrectAll(
    SchoolkidsRepository $repo,
    EntityManagerInterface $em,
    AddressCorrectionService $correctionService
): Response {
    $schoolkids = $repo->findAll();
    $correctedCount = 0;
    $skippedCount = 0;

    foreach ($schoolkids as $kid) {
        $original = [
            'street' => $kid->getStreet(),
            'streetNumber' => $kid->getStreetNumber(),
            'zip' => $kid->getZip(),
            'city' => $kid->getCity(),
            'district' => $kid->getDistrict(),
        ];

        $suggestion = $correctionService->suggestCorrection(
            $kid->getZip(),
            $kid->getStreet(),
            $kid->getCity(),
            $kid->getStreetNumber()
        );

        if (!$suggestion) {
            $skippedCount++;
            continue;
        }

        $updated = false;

        if ($suggestion['street'] !== $kid->getStreet()) {
            $kid->setStreet($suggestion['street']);
            $updated = true;
        }
        if ($suggestion['houseNumber'] !== $kid->getStreetNumber()) {
            $kid->setStreetNumber($suggestion['houseNumber']);
            $updated = true;
        }
        if ($suggestion['postcode'] !== $kid->getZip()) {
            $kid->setZip($suggestion['postcode']);
            $updated = true;
        }
        if ($suggestion['city'] !== $kid->getCity()) {
            $kid->setCity($suggestion['city']);
            $updated = true;
        }
        if ($suggestion['district'] !== $kid->getDistrict()) {
            $kid->setDistrict($suggestion['district']);
            $updated = true;
        }

        if ($updated) {
            $correctedCount++;
        }
    }

    $em->flush();

    $this->addFlash('success', "$correctedCount Einträge automatisch korrigiert. $skippedCount wurden übersprungen.");

    return $this->redirectToRoute('app_schoolkids_index');
}
#[Route('/api/schoolkids/by-school/{schoolId}', name: 'api_schoolkids_by_school', methods: ['GET'])]
public function getBySchool(int $schoolId, SchoolkidsRepository $repo): JsonResponse
{
    $kids = $repo->findBy(['school' => $schoolId]);
    $data = [];
    foreach ($kids as $kid) {
        $data[] = [
            'id' => $kid->getId(),
            'label' => $kid->getLastName() . ', ' . $kid->getFirstName()
        ];
    }
    return $this->json($data);
}

}
