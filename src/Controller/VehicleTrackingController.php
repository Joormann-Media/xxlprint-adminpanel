<?php

namespace App\Controller;

use App\Entity\VehicleTracking;
use App\Form\VehicleTrackingType;
use App\Repository\EmployeeRepository;
use App\Repository\VehicleRepository;
use App\Repository\VehicleTrackingRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Route('/vehicle-tracking')]
final class VehicleTrackingController extends AbstractController
{
    public function __construct(private readonly CacheInterface $cache) {}

    #[Route(name: 'app_vehicle_tracking_index', methods: ['GET'])]
    public function index(
        VehicleTrackingRepository $vehicleTrackingRepository,
        EmployeeRepository $employeeRepo
    ): Response {
        // Optional: Für die Tabelle weiter nur die letzten 500 Zeilen (unverändert)
        $trackings = $vehicleTrackingRepository->findBy([], ['timestamp' => 'DESC'], 500);

        $enriched = [];
        foreach ($trackings as $t) {
            $driverName = null;
            if ($t->getDriverId()) {
                $employee = $employeeRepo->findOneBy(['employeeNumber' => $t->getDriverId()]);
                $driverName = $employee
                    ? trim(($employee->getFirstname() ?? '') . ' ' . ($employee->getLastname() ?? ''))
                    : $t->getDriverId();
            }

            $enriched[] = [
                'entity'  => $t,
                'vehicle' => $t->getVehicle() ? $t->getVehicle()->getLicensePlate() : '-',
                'driver'  => $driverName ?? '-',
            ];
        }

        return $this->render('vehicle_tracking/index.html.twig', [
            'vehicle_trackings' => $enriched,
        ]);
    }


    #[Route('/new', name: 'app_vehicle_tracking_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicleTracking = new VehicleTracking();
        $form = $this->createForm(VehicleTrackingType::class, $vehicleTracking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vehicleTracking);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicle_tracking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle_tracking/new.html.twig', [
            'vehicle_tracking' => $vehicleTracking,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_vehicle_tracking_show', methods: ['GET'])]
    public function show(VehicleTracking $vehicleTracking): Response
    {
        return $this->render('vehicle_tracking/show.html.twig', [
            'vehicle_tracking' => $vehicleTracking,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_vehicle_tracking_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VehicleTracking $vehicleTracking, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehicleTrackingType::class, $vehicleTracking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicle_tracking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle_tracking/edit.html.twig', [
            'vehicle_tracking' => $vehicleTracking,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_vehicle_tracking_delete', methods: ['POST'])]
    public function delete(Request $request, VehicleTracking $vehicleTracking, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicleTracking->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vehicleTracking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vehicle_tracking_index', [], Response::HTTP_SEE_OTHER);
    }
#[Route('/import', name: 'app_vehicle_tracking_import', methods: ['POST'])]
public function importFromApi(
    Request $request,
    EntityManagerInterface $entityManager,
    VehicleRepository $vehicleRepository,
    ValidatorInterface $validator
): JsonResponse {
    try {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Ungültiges JSON oder kein Body erhalten.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Pflichtfelder prüfen
        $required = ['vehicleId', 'latitude', 'longitude', 'timestamp'];
        foreach ($required as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                return new JsonResponse([
                    'success' => false,
                    'error' => "Pflichtfeld '$field' fehlt oder ist leer."
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        // Fahrzeug suchen
        $vehicle = $vehicleRepository->findOneBy(['ortlogTransid' => $data['vehicleId']]);
        if (!$vehicle) {
            return new JsonResponse([
                'success' => false,
                'error' => "Fahrzeug mit ID {$data['vehicleId']} nicht gefunden."
            ], Response::HTTP_NOT_FOUND);
        }

        // Neues Tracking anlegen
        $tracking = new VehicleTracking();
        $tracking->setVehicle($vehicle);
        $tracking->setLatitude($data['latitude']);
        $tracking->setLongitude($data['longitude']);

        // Timestamp parsen
        try {
            $tracking->setTimestamp(new \DateTime($data['timestamp']));
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => "Ungültiges Zeitformat für 'timestamp': {$data['timestamp']}",
                'exception' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        $tracking->setSpeed($data['speed'] ?? null);
        $tracking->setCourse($data['course'] ?? null);
        $tracking->setStreet($data['street'] ?? null);
        $tracking->setCity($data['city'] ?? null);
        $tracking->setPostalcode($data['postalcode'] ?? null);
        $tracking->setDisplay($data['display'] ?? null);

        // Kilometerstand
        $kmCounter = $data['kmCounter'] ?? $data['KmCounter'] ?? null;
        $tracking->setKmCounter($kmCounter);

        // FahrerId/Personalnummer direkt speichern
        if (!empty($data['driverId'])) {
            $tracking->setDriverId($data['driverId']); // <-- neues Feld in der Entität
        }

        // Validierung
        $errors = $validator->validate($tracking);
        if (count($errors) > 0) {
            $errorList = [];
            foreach ($errors as $error) {
                $errorList[] = [
                    'property' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }
            return new JsonResponse([
                'success' => false,
                'error' => 'Validierungsfehler',
                'validation' => $errorList
            ], Response::HTTP_BAD_REQUEST);
        }

        // Speichern
        $entityManager->persist($tracking);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'id' => $tracking->getId()
        ]);
    } catch (\Throwable $e) {
        return new JsonResponse([
            'success' => false,
            'error' => 'Unerwarteter Fehler.',
            'exception' => $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}



     #[Route('/map/data', name: 'app_vehicle_tracking_map_data', methods: ['GET'])]
    public function mapDataLatestPerVehicle(
        EntityManagerInterface $em,
        VehicleRepository $vehicleRepo,
        EmployeeRepository $employeeRepo
    ): JsonResponse {
        /** @var Connection $conn */
        $conn = $em->getConnection();

        $points = $this->cache->get('vehicle_tracking_map_points_v1', function (ItemInterface $item) use ($conn, $vehicleRepo, $employeeRepo) {
            $item->expiresAfter(5); // 5 Sekunden reichen für „live“-Gefühl

            // 1) Neuester Datensatz je Fahrzeug via Window Function
            $sql = <<<SQL
                SELECT *
                FROM (
                  SELECT
                    vt.id,
                    vt.vehicle_id,
                    vt.latitude,
                    vt.longitude,
                    vt.timestamp,
                    vt.speed,
                    vt.course,
                    vt.street,
                    vt.city,
                    vt.postalcode,
                    vt.km_counter,
                    vt.driver_id,
                    ROW_NUMBER() OVER (
                      PARTITION BY vt.vehicle_id
                      ORDER BY vt.timestamp DESC, vt.id DESC
                    ) AS rn
                  FROM vehicle_tracking vt
                  WHERE vt.latitude IS NOT NULL AND vt.longitude IS NOT NULL
                ) x
                WHERE x.rn = 1
            SQL;

            $rows = $conn->fetchAllAssociative($sql);
            if (!$rows) {
                return [];
            }

            // 2) Fahrzeuge batch-laden
            $vehicleIds = array_values(array_unique(array_column($rows, 'vehicle_id')));
            $vehicles = $vehicleRepo->findBy(['id' => $vehicleIds]);
            $vehicleMap = [];
            foreach ($vehicles as $v) {
                $vehicleMap[$v->getId()] = $v;
            }

            // 3) Fahrer batch-laden
            $driverIds = [];
            foreach ($rows as $r) {
                if (!empty($r['driver_id'])) {
                    $driverIds[] = $r['driver_id'];
                }
            }
            $driverIds = array_values(array_unique($driverIds));

            $employeeMap = [];
            if ($driverIds) {
                $emps = $employeeRepo->findBy(['employeeNumber' => $driverIds]);
                foreach ($emps as $e) {
                    $num = $e->getEmployeeNumber();
                    $employeeMap[$num] = trim(($e->getFirstname() ?? '') . ' ' . ($e->getLastname() ?? '')) ?: $num;
                }
            }

            // 4) Punkte bauen
            $points = [];
            foreach ($rows as $r) {
                $vid = (int)$r['vehicle_id'];
                $veh = $vehicleMap[$vid] ?? null;

                $driverName = '-';
                if (!empty($r['driver_id'])) {
                    $driverName = $employeeMap[$r['driver_id']] ?? $r['driver_id'];
                }

                $points[] = [
                    'vehicleId'  => $vid,
                    'vehicle'    => $veh?->getLicensePlate() ?? '-',
                    'vehicleNr'  => $veh?->getVehicleNumber() ?? '-',
                    'lat'        => (float)$r['latitude'],
                    'lng'        => (float)$r['longitude'],
                    'time'       => $r['timestamp'] ? (new \DateTime($r['timestamp']))->format('Y-m-d H:i:s') : null,
                    'driver'     => $driverName,
                    'speed'      => isset($r['speed']) ? (float)$r['speed'] : null,
                    'course'     => isset($r['course']) ? (float)$r['course'] : null,
                    'street'     => $r['street'] ?? null,
                    'city'       => $r['city'] ?? null,
                    'postalcode' => $r['postalcode'] ?? null,
                    'kmCounter'  => $r['km_counter'] ?? 'unknown',
                ];
            }

            return $points;
        });

        return $this->json($points);
    }
    #[Route('/import-bulk', name: 'app_vehicle_tracking_import_bulk', methods: ['POST'])]
public function importBulk(
    Request $request,
    EntityManagerInterface $em,
    VehicleRepository $vehicleRepository,
    ValidatorInterface $validator
): JsonResponse {
    try {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return $this->json([
                'success' => false,
                'error'   => 'Erwarte ein JSON-Array von Objekten.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // --- 1) All required fields check (soft) & collect all external vehicle IDs
        $required = ['vehicleId', 'latitude', 'longitude', 'timestamp'];
        $extIds = [];
        $rows   = [];
        foreach ($payload as $i => $row) {
            if (!is_array($row)) continue;
            foreach ($required as $f) {
                if (!array_key_exists($f, $row) || $row[$f] === null || $row[$f] === '') {
                    // invalid row → skip and report
                    $rows[] = ['idx' => $i, 'valid' => false, 'reason' => "Pflichtfeld '$f' fehlt/leer."];
                    continue 2;
                }
            }
            $extIds[] = (string)$row['vehicleId'];
            $rows[]   = ['idx' => $i, 'valid' => true, 'row' => $row];
        }

        if (!$rows) {
            return $this->json(['success' => false, 'error' => 'Leeres Array.'], Response::HTTP_BAD_REQUEST);
        }

        // --- 2) Prefetch vehicles by external ID field (ortlogTransid)
        $extIds = array_values(array_unique($extIds));
        $qb = $em->createQueryBuilder()
            ->select('v')
            ->from(\App\Entity\Vehicle::class, 'v')
            ->where('v.ortlogTransid IN (:ids)')
            ->setParameter('ids', $extIds);
        $vehicles = $qb->getQuery()->getResult();

        $vehByExt = [];
        foreach ($vehicles as $v) {
            $vehByExt[(string)$v->getOrtlogTransid()] = $v;
        }

        // --- 3) Build entities in batches
        $unknownExtIds = [];
        $invalidRows   = [];
        $createdCount  = 0;

        $batchSize = 200;
        $i = 0;

        foreach ($rows as $meta) {
            if (!$meta['valid']) {
                $invalidRows[] = ['idx' => $meta['idx'], 'reason' => $meta['reason'] ?? 'Ungültig'];
                continue;
            }
            $row = $meta['row'];
            $ext = (string)$row['vehicleId'];
            $veh = $vehByExt[$ext] ?? null;
            if (!$veh) {
                $unknownExtIds[$ext] = true;
                continue;
            }

            $t = new VehicleTracking();
            $t->setVehicle($veh);
            $t->setLatitude((float)$row['latitude']);
            $t->setLongitude((float)$row['longitude']);

            try {
                $t->setTimestamp(new \DateTime($row['timestamp']));
            } catch (\Exception $e) {
                $invalidRows[] = [
                    'idx' => $meta['idx'],
                    'reason' => "Ungültiges Zeitformat: {$row['timestamp']}"
                ];
                continue;
            }

            $t->setSpeed(isset($row['speed']) && $row['speed'] !== '' ? (float)$row['speed'] : null);
            $t->setCourse(isset($row['course']) && $row['course'] !== '' ? (float)$row['course'] : null);
            $t->setStreet($row['street'] ?? null);
            $t->setCity($row['city'] ?? null);
            $t->setPostalcode($row['postalcode'] ?? null);
            $t->setDisplay($row['display'] ?? null);

            $kmCounter = $row['kmCounter'] ?? $row['KmCounter'] ?? null;
            $t->setKmCounter($kmCounter);

            if (!empty($row['driverId'])) {
                $t->setDriverId($row['driverId']);
            }

            $errors = $validator->validate($t);
            if (count($errors) > 0) {
                $invalidRows[] = ['idx' => $meta['idx'], 'reason' => 'Validierungsfehler'];
                continue;
            }

            $em->persist($t);
            $createdCount++;
            $i++;

            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(VehicleTracking::class); // nur die Tracking-Unit clearn
            }
        }

        if ($i % $batchSize !== 0) {
            $em->flush();
        }

        return $this->json([
            'success'        => true,
            'created'        => $createdCount,
            'invalidRows'    => $invalidRows,
            'unknownVehicleIds' => array_keys($unknownExtIds),
        ]);
    } catch (\Throwable $e) {
        return $this->json([
            'success' => false,
            'error'   => 'Unerwarteter Fehler.',
            'exception' => $e->getMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


#[Route('/route', name: 'app_vehicle_tracking_route_data', methods: ['GET'])]
public function routeData(
    Request $req,
    EntityManagerInterface $em
): Response {
    $vehicleId = $req->query->getInt('vehicleId');
    $fromStr   = $req->query->get('from'); // z.B. 2025-09-09T00:00
    $toStr     = $req->query->get('to');   // z.B. 2025-09-09T23:59
    $format    = strtolower($req->query->get('format', 'geojson'));
    $simplify  = (float)$req->query->get('simplify', '0'); // Meter Toleranz, 0 = aus

    if (!$vehicleId || !$fromStr || !$toStr) {
        return $this->json(['success'=>false,'error'=>'vehicleId, from, to sind Pflicht'], 400);
    }
    try {
        $from = new \DateTime($fromStr);
        $to   = new \DateTime($toStr);
    } catch (\Exception $e) {
        return $this->json(['success'=>false,'error'=>'Ungültiges Datumsformat'], 400);
    }

    /** @var Connection $conn */
    $conn = $em->getConnection();

    $rows = $conn->fetchAllAssociative(
        'SELECT latitude, longitude, timestamp, speed, course
         FROM vehicle_tracking
         WHERE vehicle_id = :vid AND timestamp BETWEEN :from AND :to
           AND latitude IS NOT NULL AND longitude IS NOT NULL
         ORDER BY timestamp ASC',
        ['vid'=>$vehicleId, 'from'=>$from->format('Y-m-d H:i:s'), 'to'=>$to->format('Y-m-d H:i:s')]
    );

    if (!$rows) {
        return $this->json(['type'=>'FeatureCollection','features'=>[]]); // leer, aber gültig
    }

    // Koordinaten & Stats aufbereiten
    $coords = [];
    $times  = [];
    foreach ($rows as $r) {
        $coords[] = [(float)$r['longitude'], (float)$r['latitude']];
        $times[]  = (new \DateTime($r['timestamp']))->format(\DateTime::ATOM);
    }

    if ($simplify > 0 && count($coords) > 2) {
        $coords = $this->douglasPeucker($coords, $simplify); // meter
    }

    // Distanz berechnen (Haversine)
    [$distanceMeters, $durationSec] = $this->routeStats($rows);

    if ($format === 'gpx') {
        $resp = new StreamedResponse(function() use ($rows) {
            $xml = $this->toGPX($rows);
            echo $xml;
        });
        $resp->headers->set('Content-Type', 'application/gpx+xml; charset=UTF-8');
        $resp->headers->set('Content-Disposition', 'attachment; filename="route.gpx"');
        return $resp;
    }

    // GeoJSON LineString + Properties
    $feature = [
        'type' => 'Feature',
        'geometry' => [
            'type' => 'LineString',
            'coordinates' => $coords,
        ],
        'properties' => [
            'pointCount' => count($rows),
            'distanceMeters' => $distanceMeters,
            'durationSeconds' => $durationSec,
            'avgSpeedKmh' => $durationSec > 0 ? round(($distanceMeters/1000) / ($durationSec/3600), 2) : 0,
            'startTime' => $times[0],
            'endTime' => $times[count($times)-1],
        ],
    ];

    return $this->json([
        'type' => 'FeatureCollection',
        'features' => [$feature],
    ]);
}

/** Vereinfachung (Douglas–Peucker) auf Meterbasis (WebMercator-Approx). */
private function douglasPeucker(array $coordsLonLat, float $toleranceMeters): array
{
    // sehr kompakt: wir projizieren grob in Meter (lon,lat -> x,y)
    $proj = fn($lon,$lat) => [
        $lon * 111320.0 * cos(deg2rad($lat)),
        $lat * 110540.0
    ];
    $pts = [];
    foreach ($coordsLonLat as $c) {
        $pts[] = $proj($c[0], $c[1]);
    }
    $keep = $this->dpRecursive($pts, 0, count($pts)-1, $toleranceMeters);
    // rebuild
    $out = [];
    foreach ($keep as $idx) $out[] = $coordsLonLat[$idx];
    return $out;
}
private function dpRecursive(array $pts, int $start, int $end, float $eps, array &$keepIdx = null)
{
    static $stack = [];
    if ($keepIdx === null) { $keepIdx = []; }
    if ($start === 0 && $end === count($pts)-1) {
        $keepIdx = [$start, $end];
    }
    $maxDist = -1; $index = -1;
    [$x1,$y1] = $pts[$start];
    [$x2,$y2] = $pts[$end];
    $dx = $x2-$x1; $dy = $y2-$y1;
    $len2 = $dx*$dx + $dy*$dy ?: 1e-9;

    for ($i=$start+1;$i<$end;$i++) {
        [$x0,$y0] = $pts[$i];
        $t = (($x0-$x1)*$dx + ($y0-$y1)*$dy) / $len2;
        $t = max(0, min(1, $t));
        $xn = $x1 + $t*$dx; $yn = $y1 + $t*$dy;
        $dist = hypot($x0-$xn, $y0-$yn);
        if ($dist > $maxDist) { $maxDist = $dist; $index = $i; }
    }
    if ($maxDist > $eps && $index > 0) {
        $this->dpRecursive($pts, $start, $index, $eps, $keepIdx);
        $this->dpRecursive($pts, $index, $end, $eps, $keepIdx);
    } else {
        // beide Enden sind schon drin; nichts tun
    }
    // am Ende: eindeutige, sortierte Indizes zurückgeben
    $uniq = array_values(array_unique($keepIdx));
    sort($uniq);
    return $uniq;
}

/** Distanz/Dauer aus Rohzeilen. */
private function routeStats(array $rows): array
{
    $dist = 0.0;
    $prev = null;
    $t0 = null; $t1 = null;
    foreach ($rows as $r) {
        $lat = (float)$r['latitude']; $lon = (float)$r['longitude'];
        $ts  = new \DateTime($r['timestamp']);
        if ($t0 === null) $t0 = $ts;
        $t1 = $ts;
        if ($prev) {
            $dist += $this->haversine($prev['lat'],$prev['lon'],$lat,$lon);
        }
        $prev = ['lat'=>$lat,'lon'=>$lon];
    }
    $dur = $t0 && $t1 ? max(0, $t1->getTimestamp() - $t0->getTimestamp()) : 0;
    return [$dist, $dur];
}
private function haversine(float $lat1,float $lon1,float $lat2,float $lon2): float
{
    $R=6371000.0;
    $dLat=deg2rad($lat2-$lat1);
    $dLon=deg2rad($lon2-$lon1);
    $a=sin($dLat/2)**2 + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLon/2)**2;
    $c=2*atan2(sqrt($a), sqrt(1-$a));
    return $R*$c; // meter
}

/** GPX aus Rows (Track). */
private function toGPX(array $rows): string
{
    $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><gpx version="1.1" creator="Tekath-Panel" xmlns="http://www.topografix.com/GPX/1/1"></gpx>');
    $trk = $xml->addChild('trk');
    $trk->addChild('name', 'Vehicle Route');
    $seg = $trk->addChild('trkseg');
    foreach ($rows as $r) {
        $pt = $seg->addChild('trkpt');
        $pt->addAttribute('lat', (string)$r['latitude']);
        $pt->addAttribute('lon', (string)$r['longitude']);
        $pt->addChild('time', (new \DateTime($r['timestamp']))->format(\DateTime::ATOM));
        if ($r['speed'] !== null) {
            $pt->addChild('extensions')
               ->addChild('speed', (string)$r['speed']);
        }
    }
    // SimpleXMLElement -> String
    $dom = dom_import_simplexml($xml)->ownerDocument;
    $dom->formatOutput = true;
    return $dom->saveXML();
}
#[Route('/route/view', name: 'app_vehicle_tracking_route_view', methods: ['GET'])]
public function routeView(VehicleRepository $vehRepo): Response
{
    $vehicles = $vehRepo->findBy([], ['vehicleNumber' => 'ASC']);
    return $this->render('vehicle_tracking/route.html.twig', [
        'vehicles' => $vehicles,
        'tiles_style_url' => $_ENV['TILES_STYLE_URL'] ?? 'https://tiles.joormann-media.de/styles/basic/style.json',
    ]);
}
#[Route('/follow', name: 'app_vehicle_tracking_follow_view', methods: ['GET'])]
public function followView(Request $req, VehicleRepository $vehRepo): Response
{
    $vehicles = $vehRepo->findBy([], ['vehicleNumber' => 'ASC']);

    return $this->render('vehicle_tracking/follow.html.twig', [
        'vehicles' => $vehicles,
        'tiles_style_url' => $_ENV['TILES_STYLE_URL'] ?? 'https://tiles.joormann-media.de/styles/basic/style.json',
        'initial_vehicle_id' => $req->query->getInt('vehicleId') ?: null, // <— neu
    ]);
}


#[Route('/live', name: 'app_vehicle_tracking_live', methods: ['GET'])]
public function livePoint(
    Request $req,
    EntityManagerInterface $em,
    EmployeeRepository $employeeRepo
): JsonResponse {
    $vehicleId = $req->query->getInt('vehicleId');
    if (!$vehicleId) {
        return $this->json(['success'=>false,'error'=>'vehicleId fehlt'], 400);
    }

    /** @var Connection $conn */
    $conn = $em->getConnection();
    $row = $conn->fetchAssociative(
        'SELECT vt.vehicle_id, vt.latitude, vt.longitude, vt.timestamp, vt.speed, vt.course,
                vt.street, vt.city, vt.postalcode, vt.driver_id
         FROM vehicle_tracking vt
         WHERE vt.vehicle_id = :vid
           AND vt.latitude IS NOT NULL AND vt.longitude IS NOT NULL
         ORDER BY vt.timestamp DESC, vt.id DESC
         LIMIT 1',
        ['vid'=>$vehicleId]
    );

    if (!$row) {
        return $this->json(['success'=>true,'data'=>null]);
    }

    $driverName = null;
    if (!empty($row['driver_id'])) {
        $emp = $employeeRepo->findOneBy(['employeeNumber' => $row['driver_id']]);
        $driverName = $emp ? trim(($emp->getFirstname() ?? '').' '.($emp->getLastname() ?? '')) : $row['driver_id'];
    }

    return $this->json([
        'success'=>true,
        'data'=>[
            'vehicleId'  => (int)$row['vehicle_id'],
            'lat'        => (float)$row['latitude'],
            'lng'        => (float)$row['longitude'],
            'time'       => (new \DateTime($row['timestamp']))->format(\DateTime::ATOM),
            'speed'      => $row['speed'] !== null ? (float)$row['speed'] : null,
            'course'     => $row['course'] !== null ? (float)$row['course'] : null,
            'street'     => $row['street'] ?? null,
            'city'       => $row['city'] ?? null,
            'postalcode' => $row['postalcode'] ?? null,
            'driver'     => $driverName,
        ]
    ]);
}


}








