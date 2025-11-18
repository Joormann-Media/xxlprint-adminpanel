<?php

namespace App\Service;

use App\Entity\OfficialAddress;
use App\Repository\OfficialAddressRepository;
use Psr\Log\LoggerInterface;

class AddressCorrectionService
{
    public function __construct(
        private readonly OfficialAddressRepository $addressRepo,
        private readonly LoggerInterface $logger
    ) {}

    public function suggestCorrection(
        string $postcode,
        string $street,
        ?string $city = null,
        ?string $houseNumber = null
    ): ?array {
        $originalPostcode = $postcode;
        $postcode = $this->sanitizePostcode($postcode);

        $this->logger->info("[AddressCorrection] Anfrage für: {$street} {$houseNumber}, {$postcode} {$city}");

        if ($postcode !== $originalPostcode) {
            $this->logger->info("[AddressCorrection] Ursprüngliche PLZ '{$originalPostcode}' korrigiert zu '{$postcode}'");
        }

        $localSuggestion = $this->suggestFromLocal($postcode, $street, $city, $houseNumber);
        if ($localSuggestion !== null) {
            $localSuggestion['source'] = 'local';
            $this->logger->info('[AddressCorrection] Treffer aus lokaler DB.', $localSuggestion);
            return $localSuggestion;
        }

        $externalSuggestion = $this->suggestFromExternal($postcode, $street, $city, $houseNumber);
        if ($externalSuggestion !== null) {
            $externalSuggestion['source'] = 'external';
            $this->logger->info('[AddressCorrection] Treffer von externer API.', $externalSuggestion);
            return $externalSuggestion;
        }

        $this->logger->warning('[AddressCorrection] Keine passende Adresse gefunden.');
        return null;
    }

    public function suggestFromLocal(
        string $postcode,
        string $street,
        ?string $city = null,
        ?string $houseNumber = null
    ): ?array {
        $postcode = trim($postcode);
        $street = trim($street);
        $candidates = [];

        if ($this->isValidPostcode($postcode)) {
            $candidates = $this->addressRepo->findBy(['postcode' => $postcode]);
        } elseif ($city) {
            $candidates = $this->addressRepo->findByCity($city);
        } else {
            $this->logger->warning("[AddressCorrection] Ungültige PLZ und keine Stadt vorhanden für Fallback.");
            return null;
        }

        $bestMatch = null;
        $bestScore = -1;

        foreach ($candidates as $candidate) {
            $score = 100 - levenshtein(
                mb_strtolower($street),
                mb_strtolower($candidate->getStreet())
            );

            if ($city && mb_strtolower($candidate->getCity()) === mb_strtolower($city)) {
                $score += 10;
            }

            if ($houseNumber && $candidate->getHouseNumber() === $houseNumber) {
                $score += 5;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $candidate;
            }
        }

        if ($bestScore >= 70 && $bestMatch) {
            return [
                'postcode' => $bestMatch->getPostcode(),
                'city' => $bestMatch->getCity(),
                'district' => $bestMatch->getDistrict(),
                'street' => $bestMatch->getStreet(),
                'houseNumber' => $bestMatch->getHouseNumber(),
            ];
        }

        return null;
    }

    public function suggestFromExternal(
        string $postcode,
        string $street,
        ?string $city = null,
        ?string $houseNumber = null
    ): ?array {
        $query = http_build_query([
            'street' => $houseNumber ? "$houseNumber $street" : $street,
            'city' => $city ?? '',
            'postalcode' => $postcode,
            'country' => 'Germany',
            'format' => 'json',
            'limit' => 1,
            'addressdetails' => 1,
        ]);

        $url = "https://nominatim.openstreetmap.org/search?$query";

        $opts = [
            'http' => [
                'header' => "User-Agent: TekathControlPanel/1.0\r\n",
                'timeout' => 5,
            ]
        ];

        $context = stream_context_create($opts);
        $json = @file_get_contents($url, false, $context);

        if (!$json) {
            $this->logger->error('[AddressCorrection] Nominatim konnte nicht erreicht werden.');
            return null;
        }

        $data = json_decode($json, true);
        if (!isset($data[0])) {
            $this->logger->warning('[AddressCorrection] Nominatim hat keine Ergebnisse geliefert.');
            return null;
        }

        $addr = $data[0]['address'] ?? [];

        $resolvedCity = $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $city;
        $rawDistrict = $addr['suburb']
            ?? $addr['neighbourhood']
            ?? $addr['quarter']
            ?? $addr['city_district']
            ?? null;

        // District nur übernehmen, wenn er sich unterscheidet von City
        $district = ($rawDistrict && strtolower($rawDistrict) !== strtolower($resolvedCity)) ? $rawDistrict : null;

        $this->logger->debug("[AddressCorrection] Raw district: {$rawDistrict} | Final city: {$resolvedCity} | Used: " . ($district ?? 'null'));

        return [
            'postcode' => $addr['postcode'] ?? $postcode,
            'city' => $resolvedCity,
            'district' => $district,
            'street' => $addr['road'] ?? $street,
            'houseNumber' => $addr['house_number'] ?? $houseNumber,
        ];
    }

    public function suggestFor(string $street, ?string $houseNumber, string $postcode, ?string $city = null): ?array
    {
        return $this->suggestCorrection($postcode, $street, $city, $houseNumber);
    }

    public function isValidPostcode(string $postcode): bool
    {
        return preg_match('/^\d{5}$/', trim($postcode)) === 1;
    }

    public function sanitizePostcode(string $postcode): string
    {
        $cleaned = preg_replace('/[^\d]/', '', $postcode);
        return substr($cleaned, 0, 5);
    }
}
