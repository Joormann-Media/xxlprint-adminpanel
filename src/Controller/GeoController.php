<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GeoController
{
    #[Route('/api/geocode', name: 'api_geocode', methods: ['GET'])]
    public function geocode(Request $request): JsonResponse
    {
        $q = $request->query->get('q');
        if (!$q || strlen($q) < 3) {
            return new JsonResponse([], 400);
        }

        // Anfrage an Nominatim
        $url = 'https://nominatim.openstreetmap.org/search?format=json&countrycodes=de&q=' . urlencode($q);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'TekathPanel/1.0 (bea@tekath.de)'); // <-- Immer setzen!
        $data = curl_exec($ch);
        curl_close($ch);

        // Optional: Fehler-Handling, falls kein valides JSON!
        $result = json_decode($data, true);
        if (!is_array($result)) {
            return new JsonResponse([], 502); // Bad Gateway
        }

        return new JsonResponse($result);
    }
}
