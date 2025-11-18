<?php

namespace App\Controller;

use App\Entity\TaxiCalculator;
use App\Form\TaxiCalculatorType;
use App\Form\TaxiFareCalculatorType;
use App\Repository\TaxiCalculatorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/taxi-calculator')]
final class TaxiCalculatorController extends AbstractController
{
    #[Route(name: 'app_taxi_calculator_index', methods: ['GET'])]
    public function index(TaxiCalculatorRepository $taxiCalculatorRepository): Response
    {
        return $this->render('taxi_calculator/index.html.twig', [
            'taxi_calculators' => $taxiCalculatorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_taxi_calculator_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $taxiCalculator = new TaxiCalculator();
        $form = $this->createForm(TaxiCalculatorType::class, $taxiCalculator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taxiCalculator);
            $entityManager->flush();

            return $this->redirectToRoute('app_taxi_calculator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('taxi_calculator/new.html.twig', [
            'taxi_calculator' => $taxiCalculator,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_taxi_calculator_show', methods: ['GET'])]
    public function show(TaxiCalculator $taxiCalculator): Response
    {
        return $this->render('taxi_calculator/show.html.twig', [
            'taxi_calculator' => $taxiCalculator,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_taxi_calculator_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TaxiCalculator $taxiCalculator, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaxiCalculatorType::class, $taxiCalculator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_taxi_calculator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('taxi_calculator/edit.html.twig', [
            'taxi_calculator' => $taxiCalculator,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_taxi_calculator_delete', methods: ['POST'])]
    public function delete(Request $request, TaxiCalculator $taxiCalculator, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taxiCalculator->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($taxiCalculator);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_taxi_calculator_index', [], Response::HTTP_SEE_OTHER);
    }

    // -------- API-Proxy für Adresssuche (Geocoding) ---------
    #[Route('/api/geocode', name: 'api_geocode', methods: ['GET'])]
    public function geocode(Request $request): JsonResponse
    {
        $q = $request->query->get('q');
        if (!$q || strlen($q) < 3) {
            return new JsonResponse([], 400);
        }
        $url = 'https://nominatim.openstreetmap.org/search?format=json&countrycodes=de&q=' . urlencode($q);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'TekathPanel/1.0 (bea@tekath.de)');
        $data = curl_exec($ch);
        curl_close($ch);
        return new JsonResponse(json_decode($data, true));
    }

    // --------- API: Preisberechnung per Route (OpenRouteService) ----------
    #[Route('/api/calculate-route', name: 'api_taxi_calculate_route', methods: ['POST'])]
    public function apiCalculateRoute(
        Request $request,
        TaxiCalculatorRepository $taxiCalculatorRepository
    ): JsonResponse {
        $rawContent = $request->getContent();
        $data = json_decode($rawContent, true);

        if (!$data || !is_array($data)) {
            return $this->json(['error' => 'Ungültiges JSON!'], 400);
        }

        $start = $data['start'] ?? null;
        $end = $data['end'] ?? null;
        $datetime = $data['datetime'] ?? null;

        if (
            !$start || !$end ||
            !isset($start['lat'], $start['lon'], $end['lat'], $end['lon'])
        ) {
            return $this->json(['error' => 'Start/Ziel fehlen oder Koordinaten sind ungültig!'], 400);
        }

        $startLat = (float)$start['lat'];
        $startLon = (float)$start['lon'];
        $endLat   = (float)$end['lat'];
        $endLon   = (float)$end['lon'];

        // ORS-URL aus ENV oder Fallback
        $orsUrl = $_ENV['ORS_URL'] ?? 'http://127.0.0.1:8082/ors/v2/directions/driving-car';

        $postFields = json_encode([
            'coordinates' => [
                [(float)$start['lon'], (float)$start['lat']],
                [(float)$end['lon'], (float)$end['lat']]
            ],
            'geometry' => true,
            
        ]);

        try {
            $ch = curl_init($orsUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

            $routeResponse = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($routeResponse === false || $httpStatus !== 200) {
                return $this->json([
                    'error' => 'ORS-Call fehlgeschlagen!',
                    'details' => $curlError ?: $routeResponse
                ], 500);
            }

            $routeData = json_decode($routeResponse, true);

            if (isset($routeData['error'])) {
                return $this->json([
                    'error' => $routeData['error']['message'] ?? 'ORS Fehler',
                    'code'  => $routeData['error']['code'] ?? null
                ], 400);
            }

            $summary = $routeData['routes'][0]['summary'] ?? null;
            $segment = $routeData['routes'][0]['segments'][0] ?? null;

            if (!$summary && !$segment) {
                return $this->json(['error' => 'Route unvollständig oder nicht gefunden!'], 400);
            }

            $distance = $summary['distance'] / 1000 ?? $segment['distance'] / 1000;
            $duration = $summary['duration'] ?? $segment['duration'];

            $dt = $datetime ? new \DateTime($datetime) : new \DateTime();
            $hour = (int)$dt->format('H');
            $weekday = (int)$dt->format('w'); // 0=Sonntag
            $isNight = ($hour < 6 || $hour >= 23 || $weekday === 0);

            $tarif = $taxiCalculatorRepository->findOneBy([], ['validFrom' => 'DESC']);
            if (!$tarif) {
                return $this->json(['error' => 'Kein Tarif in DB hinterlegt!'], 500);
            }

            $price = $isNight ? $tarif->getBaseFeeNight() : $tarif->getBaseFeeDay();
            $price += $distance * ($isNight ? $tarif->getPricePerKmNight() : $tarif->getPricePerKmDay());

            $geometry = null;
            if (isset($routeData['routes'][0]['geometry'])) {
                $polyline = $routeData['routes'][0]['geometry'];
                $geometry = $this->decodePolyline($polyline);
            }


            $response = [
                'price'    => round($price, 2),
                'distance' => $distance,
                'duration' => $duration,
                'geometry' => $geometry,
            ];

            if ($this->getParameter('kernel.debug')) {
                $response['debug'] = [
                    'request' => $postFields,
                    'httpStatus' => $httpStatus,
                    'orsRaw' => $routeResponse,
                ];
            }

            return $this->json($response);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Routenabfrage Exception',
                'exception' => $e->getMessage()
            ], 500);
        }
    }

    // ------ Klassischer Formularrechner (nur für admin/alt) ------
    #[Route('/calculator', name: 'app_taxi_calculator_calc', methods: ['GET', 'POST'])]
    public function calculator(Request $request, TaxiCalculatorRepository $taxiCalculatorRepository): Response
    {
        $form = $this->createForm(TaxiFareCalculatorType::class);
        $form->handleRequest($request);

        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $tarif = $taxiCalculatorRepository->findOneBy([], ['validFrom' => 'DESC']);

            if ($tarif) {
                $km = (float)($data['km'] ?? 0);
                $standzeitMin = (float)($data['standzeitMin'] ?? 0);
                $isNight = (bool)($data['isNight'] ?? false);
                $isLargeCab = (bool)($data['isLargeCab'] ?? false);

                $preis = $isNight ? $tarif->getBaseFeeNight() : $tarif->getBaseFeeDay();
                $preis += $km * ($isNight ? $tarif->getPricePerKmNight() : $tarif->getPricePerKmDay());

                if ($standzeitMin > 0) {
                    if ($standzeitMin <= 5) {
                        $preis += ($standzeitMin * 60 / $tarif->getWaitSectionSecondsFirst5Min()) * 0.10;
                    } else {
                        $preis += (5 * 60 / $tarif->getWaitSectionSecondsFirst5Min()) * 0.10;
                        $restMin = $standzeitMin - 5;
                        $preis += ($restMin * 60 / $tarif->getWaitSectionSecondsFrom6Min()) * 0.10;
                    }
                }

                if ($isLargeCab) {
                    $preis += $tarif->getLargeCabSurcharge();
                }

                $result = round($preis, 2);
            }
        }

        return $this->render('taxi_calculator/calc.html.twig', [
            'form' => $form,
            'result' => $result,
            'taxi_calculators' => $taxiCalculatorRepository->findAll()
        ]);
    }
    /**
 * Decode a polyline to GeoJSON LineString
 */
private function decodePolyline(string $polyline, int $precision = 5): array
{
    $coordinates = [];
    $index = $lat = $lng = 0;
    $shift = $result = 0;

    $factor = pow(10, $precision);

    while ($index < strlen($polyline)) {
        $byte = null;
        $shift = $result = 0;

        do {
            $byte = ord($polyline[$index++]) - 63;
            $result |= ($byte & 0x1f) << $shift;
            $shift += 5;
        } while ($byte >= 0x20);
        $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
        $lat += $dlat;

        $shift = $result = 0;
        do {
            $byte = ord($polyline[$index++]) - 63;
            $result |= ($byte & 0x1f) << $shift;
            $shift += 5;
        } while ($byte >= 0x20);
        $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
        $lng += $dlng;

        $coordinates[] = [ $lng / $factor, $lat / $factor ];
    }

    return [
        'type' => 'LineString',
        'coordinates' => $coordinates
    ];
}

}
