<?php

namespace App\Service;

use App\Entity\OfficialAddress;
use App\Repository\OfficialAddressRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddressImportService
{
    // Batch-Size für Flush, anpassbar je nach RAM und DB
    private const BATCH_SIZE = 1000;

    public function __construct(
        private readonly OfficialAddressRepository $addressRepo,
    ) {}

    /**
     * Importiert alle Adressen für eine Liste von PLZ (Stapel).
     * Holt Adressen **nur für PLZ, die noch nicht in der DB sind**.
     *
     * @param array<int|string> $plzList
     * @return array{addresses: OfficialAddress[], skippedPlz: array<string>}
     */
    public function getAddressesForPlzList(array $plzList): array
    {
        $results = [];
        $skippedPlz = [];

        foreach ($plzList as $plz) {
            // Gibt es schon Adressen zu dieser PLZ?
            $count = $this->addressRepo->count(['postcode' => (string)$plz]);
            if ($count > 0) {
                $skippedPlz[] = $plz;
                continue;
            }
            $batch = $this->fetchFromOverpass($plz);
            $results = array_merge($results, $batch);
            sleep(1); // Rate-Limit Overpass
        }
        return [
            'addresses' => $results,
            'skippedPlz' => $skippedPlz,
        ];
    }

    /**
     * Haupt-Importer für PLZ-Bereich (Kompatibilitäts-Methode).
     * 
     * @param int $start
     * @param int $end
     * @return OfficialAddress[]
     */
    public function getAddressesForRange(int $start, int $end): array
    {
        $results = [];
        for ($plz = $start; $plz <= $end; $plz++) {
            $batch = $this->fetchFromOverpass($plz);
            $results = array_merge($results, $batch);
            sleep(1);
        }
        return $results;
    }

    /**
     * Persistiert eine Liste von Adressen, ignoriert offensichtliche Duplikate.
     * Mit Transaktionssicherheit und Batch-Flush.
     * 
     * @param OfficialAddress[] $addresses
     * @param EntityManagerInterface $em
     * @return array
     */
    public function persist(array $addresses, EntityManagerInterface $em): array
    {
        $inserted = 0;
        $skipped = 0;
        $errors = [];
        $batchCount = 0;

        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            foreach ($addresses as $addr) {
                if (!$addr->getStreet() || !$addr->getPostcode() || !$addr->getHouseNumber()) {
                    $skipped++;
                    $errors[] = [
                        'reason' => 'Pflichtfeld fehlt (Straße, PLZ oder Hausnummer)',
                        'plz' => $addr->getPostcode(),
                        'street' => $addr->getStreet(),
                        'houseNumber' => $addr->getHouseNumber()
                    ];
                    continue;
                }

                $exists = $this->addressRepo->findOneBy([
                    'postcode' => $addr->getPostcode(),
                    'street' => $addr->getStreet(),
                    'houseNumber' => $addr->getHouseNumber(),
                ]);

                if ($exists) {
                    $skipped++;
                    $errors[] = [
                        'reason' => 'Adresse bereits vorhanden',
                        'plz' => $addr->getPostcode(),
                        'street' => $addr->getStreet(),
                        'houseNumber' => $addr->getHouseNumber()
                    ];
                    continue;
                }

                try {
                    $em->persist($addr);
                    $inserted++;
                    $batchCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'reason' => 'Datenbankfehler: ' . $e->getMessage(),
                        'plz' => $addr->getPostcode(),
                        'street' => $addr->getStreet(),
                        'houseNumber' => $addr->getHouseNumber()
                    ];
                }

                // Batch-Flush für große Datenmengen
                if ($batchCount >= self::BATCH_SIZE) {
                    $em->flush();
                    $em->clear();
                    $batchCount = 0;
                }
            }

            // Rest flushen
            if ($batchCount > 0) {
                $em->flush();
                $em->clear();
            }

            $conn->commit();

        } catch (\Exception $e) {
            $conn->rollBack();
            $errors[] = [
                'reason' => 'Transaktionsfehler: ' . $e->getMessage(),
            ];
            return [
                'inserted' => $inserted,
                'skipped' => $skipped,
                'errors' => $errors,
                'success' => false,
            ];
        }

        return [
            'inserted' => $inserted,
            'skipped' => $skipped,
            'errors' => $errors,
            'success' => true,
        ];
    }

    /**
     * Holt Adressdaten über Overpass API für eine einzelne PLZ.
     */
    public function fetchFromOverpass(int|string $plz): array
    {
        $query = <<<EOT
[out:json][timeout:25];
area["postal_code"="$plz"]->.searchArea;
(
  node["addr:street"](area.searchArea);
  way["addr:street"](area.searchArea);
);
out center;
EOT;

        $url = 'https://overpass-api.de/api/interpreter';
        $response = @file_get_contents($url . '?data=' . urlencode($query));

        if (!$response) {
            return [];
        }

        $json = json_decode($response, true);
        $results = [];

        if (!isset($json['elements'])) {
            return $results;
        }

        foreach ($json['elements'] as $el) {
            $tags = $el['tags'] ?? [];
            if (!isset($tags['addr:street'])) {
                continue;
            }

            $address = new OfficialAddress();
            $address->setPostcode((string)$plz);
            $address->setCity($tags['addr:city'] ?? 'Unbekannt');
            $address->setStreet($tags['addr:street']);
            $address->setHouseNumber($tags['addr:housenumber'] ?? null);
            $address->setDistrict($tags['addr:suburb'] ?? null);
            $address->setLat($el['lat'] ?? ($el['center']['lat'] ?? null));
            $address->setLon($el['lon'] ?? ($el['center']['lon'] ?? null));
            $address->setCountry('DE');
            $address->setSource('osm');
            $address->setCreatedAt(new \DateTimeImmutable());

            $results[] = $address;
        }

        return $results;
    }

    /**
     * Holt Adressdaten über Overpass API für eine einzelne PLZ und Straßen-Anfangsbuchstabe.
     */
    public function fetchFromOverpassByStreetLetter(int|string $plz, string $letter): array
    {
        $query = <<<EOT
[out:json][timeout:25];
area["postal_code"="$plz"]->.searchArea;
(
  node["addr:street"](area.searchArea)["addr:street"~"^$letter",i];
  way["addr:street"](area.searchArea)["addr:street"~"^$letter",i];
);
out center;
EOT;

        $url = 'https://overpass-api.de/api/interpreter';
        $response = @file_get_contents($url . '?data=' . urlencode($query));

        if (!$response) {
            return [];
        }

        $json = json_decode($response, true);
        $results = [];

        if (!isset($json['elements'])) {
            return $results;
        }

        foreach ($json['elements'] as $el) {
            $tags = $el['tags'] ?? [];
            if (!isset($tags['addr:street'])) {
                continue;
            }
            $address = new OfficialAddress();
            $address->setPostcode((string)$plz);
            $address->setCity($tags['addr:city'] ?? 'Unbekannt');
            $address->setStreet($tags['addr:street']);
            $address->setHouseNumber($tags['addr:housenumber'] ?? null);
            $address->setDistrict($tags['addr:suburb'] ?? null);
            $address->setLat($el['lat'] ?? ($el['center']['lat'] ?? null));
            $address->setLon($el['lon'] ?? ($el['center']['lon'] ?? null));
            $address->setCountry('DE');
            $address->setSource('osm');
            $address->setCreatedAt(new \DateTimeImmutable());
            $results[] = $address;
        }

        return $results;
    }

    /**
     * Ermittelt den Stadtteil (district/suburb) anhand von Lat/Lon via Nominatim.
     */
    public function resolveDistrictFromLatLon(float $lat, float $lon): ?string
    {
        $url = sprintf(
            'https://nominatim.openstreetmap.org/reverse?format=json&lat=%s&lon=%s&zoom=18&addressdetails=1',
            $lat,
            $lon
        );

        $opts = [
            'http' => [
                'header' => "User-Agent: Tekath-Panel/1.0\r\n"
            ]
        ];

        $context = stream_context_create($opts);
        $json = @file_get_contents($url, false, $context);

        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);

        return $data['address']['suburb']
            ?? $data['address']['neighbourhood']
            ?? $data['address']['quarter']
            ?? $data['address']['city_district']
            ?? null;
    }
}
