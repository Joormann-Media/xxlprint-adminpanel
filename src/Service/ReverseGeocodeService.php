<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReverseGeocodeService
{
    private const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/reverse';

    public function __construct(private HttpClientInterface $client) {}

    /**
     * Holt nur den District als String
     */
    public function getDistrictFromCoordinates(float $lat, float $lon): ?string
    {
        $result = $this->getFullAddressData($lat, $lon);
        return $result['district'] ?? null;
    }

    /**
     * Holt alle verfügbaren Addressdaten aus Nominatim für Feinzuordnung
     */
    public function getFullAddressData(float $lat, float $lon): array
    {
        $response = $this->client->request('GET', self::NOMINATIM_URL, [
            'query' => [
                'lat' => $lat,
                'lon' => $lon,
                'format' => 'json',
                'addressdetails' => 1,
            ],
            'headers' => [
                'User-Agent' => 'TekathDistrictBot/1.0',
            ]
        ]);

        $data = $response->toArray();

        $address = $data['address'] ?? [];

        return [
            'district'        => $address['suburb'] ?? $address['neighbourhood'] ?? $address['city_district'] ?? $address['quarter'] ?? null,
            'neighbourhood'   => $address['neighbourhood'] ?? null,
            'subdistrict'     => $address['quarter'] ?? $address['city_district'] ?? null,
            'locationComment' => $data['display_name'] ?? null,
        ];
    }
}
