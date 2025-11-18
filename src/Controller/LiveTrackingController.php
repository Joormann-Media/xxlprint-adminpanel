<?php

namespace App\Controller;

use App\Repository\EmployeeRepository;
use App\Repository\VehicleRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/live')]
final class LiveTrackingController extends AbstractController
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {}

    #[Route('', name: 'app_live_tracking', methods: ['GET'])]
    public function index(): Response
    {
        // Nur Template, Daten kommen per AJAX
        return $this->render('live_tracking/index.html.twig');
    }

    #[Route('/map/data', name: 'app_live_tracking_map_data', methods: ['GET'])]
    public function mapData(
        EmployeeRepository $employeeRepo,
        VehicleRepository $vehicleRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        /** @var Connection $conn */
        $conn = $em->getConnection();

        // Kurzzeit-Caching gegen Poll-Last (Frontend ruft alle 10s)
        $points = $this->cache->get('live_map_points_v1', function (ItemInterface $item) use ($conn, $employeeRepo, $vehicleRepo) {
            $item->expiresAfter(5); // 5 Sekunden reichen für "live"

            // --- 1) Neueste Tracking-Reihe je Fahrzeug via Window Function (schnell & eindeutig) ---
            // Voraussetzungen: MySQL 8+/MariaDB 10.2+/PostgreSQL 10+
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

            // --- 2) Batch-Resolve Fahrzeuge + Fahrer (kein N+1!) ---
            $vehicleIds = array_values(array_unique(array_column($rows, 'vehicle_id')));
            $vehicles = $vehicleRepo->findBy(['id' => $vehicleIds]);
            $vehicleMap = [];
            foreach ($vehicles as $v) {
                $vehicleMap[$v->getId()] = $v;
            }

            // Fahrer-IDs sammeln (nur nicht-null/nicht-leer)
            $driverIds = [];
            foreach ($rows as $r) {
                if (!empty($r['driver_id'])) {
                    $driverIds[] = $r['driver_id'];
                }
            }
            $driverIds = array_values(array_unique($driverIds));

            $employeeMap = [];
            if ($driverIds) {
                // Hinweis: falls employeeNumber alphanumerisch ist, funktioniert das (Doctrine wandelt IN (…) korrekt)
                $employees = $employeeRepo->findBy(['employeeNumber' => $driverIds]);
                foreach ($employees as $e) {
                    $num = $e->getEmployeeNumber();
                    $employeeMap[$num] = trim(($e->getFirstName() ?? '') . ' ' . ($e->getLastName() ?? '')) ?: $num;
                }
            }

            // --- 3) Points bauen ---
            $points = [];
            foreach ($rows as $r) {
                $vid = (int) $r['vehicle_id'];
                $veh = $vehicleMap[$vid] ?? null;

                $driverName = '-';
                if (!empty($r['driver_id'])) {
                    $driverName = $employeeMap[$r['driver_id']] ?? $r['driver_id'];
                }

                $points[] = [
                    'vehicleId'  => $vid,
                    'vehicle'    => $veh?->getLicensePlate() ?? '-',
                    'vehicleNr'  => $veh?->getVehicleNumber() ?? '-',
                    'lat'        => (float) $r['latitude'],
                    'lng'        => (float) $r['longitude'],
                    'time'       => $r['timestamp'] ? (new \DateTime($r['timestamp']))->format('Y-m-d H:i:s') : null,
                    'driver'     => $driverName,
                    'speed'      => isset($r['speed']) ? (float) $r['speed'] : null,
                    'course'     => isset($r['course']) ? (float) $r['course'] : null,
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

    #[Route('/map/data/{id<\d+>}', name: 'app_live_tracking_map_data_single', methods: ['GET'])]
    public function mapDataSingle(
        int $id,
        EmployeeRepository $employeeRepo,
        EntityManagerInterface $em,
        VehicleRepository $vehicleRepo,
    ): JsonResponse {
        /** @var Connection $conn */
        $conn = $em->getConnection();

        // Einzel-Fahrzeug: simpler und performant mit Index (vehicle_id, timestamp DESC)
        $sql = <<<SQL
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
              vt.driver_id
            FROM vehicle_tracking vt
            WHERE vt.vehicle_id = :vid
              AND vt.latitude IS NOT NULL
              AND vt.longitude IS NOT NULL
            ORDER BY vt.timestamp DESC, vt.id DESC
            LIMIT 1
        SQL;

        $row = $conn->fetchAssociative($sql, ['vid' => $id]);

        if (!$row) {
            return $this->json(['error' => 'Kein Tracking gefunden'], 404);
        }

        $vehicle = $vehicleRepo->find($id);

        $driverName = '-';
        if (!empty($row['driver_id'])) {
            $employee = $employeeRepo->findOneBy(['employeeNumber' => $row['driver_id']]);
            $driverName = $employee
                ? trim(($employee->getFirstName() ?? '') . ' ' . ($employee->getLastName() ?? '')) ?: $row['driver_id']
                : $row['driver_id'];
        }

        return $this->json([
            'vehicleId'  => (int) $row['vehicle_id'],
            'vehicle'    => $vehicle?->getLicensePlate() ?? '-',
            'vehicleNr'  => $vehicle?->getVehicleNumber() ?? '-',
            'lat'        => (float) $row['latitude'],
            'lng'        => (float) $row['longitude'],
            'time'       => $row['timestamp'] ? (new \DateTime($row['timestamp']))->format('Y-m-d H:i:s') : null,
            'driver'     => $driverName,
            'speed'      => isset($row['speed']) ? (float) $row['speed'] : null,
            'course'     => isset($row['course']) ? (float) $row['course'] : null,
            'street'     => $row['street'] ?? null,
            'city'       => $row['city'] ?? null,
            'postalcode' => $row['postalcode'] ?? null,
            'kmCounter'  => $row['km_counter'] ?? 'unknown',
        ]);
    }

    #[Route('/follow/{id<\d+>}', name: 'app_live_tracking_follow', methods: ['GET'])]
    public function follow(int $id, VehicleRepository $vehicleRepo): Response
    {
        $vehicle = $vehicleRepo->find($id);

        return $this->render('live_tracking/follow.html.twig', [
            'vehicle' => $vehicle,
        ]);
    }
}
