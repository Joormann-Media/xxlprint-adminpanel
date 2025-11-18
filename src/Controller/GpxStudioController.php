<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gpx-studio')]
final class GpxStudioController extends AbstractController
{
    #[Route('', name: 'app_gpx_studio', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('gpx_studio/index.html.twig', [
            'tiles_style_url' => $_ENV['TILES_STYLE_URL'] ?? 'https://tiles.joormann-media.de/styles/basic/style.json',
            'default_profile' => $_ENV['ORS_DEFAULT_PROFILE'] ?? 'driving-car',
        ]);
    }

    /**
     * ORS Base-URL ermitteln (mit/ohne /ors-Prefix möglich).
     * Empfohlen in .env.local: ORS_BASE_URL="http://127.0.0.1:8082/ors"
     */
    private function orsBase(): string
    {
        return rtrim($_ENV['ORS_BASE_URL'] ?? 'http://127.0.0.1:8082/ors', '/');
    }

    /** HttpClient mit Timeout; TLS-Verify per Default an (ändere bei Bedarf). */
    private function http()
    {
        return HttpClient::create([
            'timeout' => 45,
            // Bei internem Self-Signed TLS ggf. entkommentieren (nur intern!):
            // 'verify_peer' => false,
            // 'verify_host' => false,
        ]);
    }

    /** Minimale Koordinatenvalidierung + Float-Cast. */
    private function sanitizeCoordinates(mixed $coords): array
    {
        $out = [];
        if (!is_array($coords)) {
            return $out;
        }
        foreach ($coords as $c) {
            if (is_array($c) && count($c) >= 2 && is_numeric($c[0]) && is_numeric($c[1])) {
                $out[] = [ (float)$c[0], (float)$c[1] ];
            }
        }
        return $out;
    }

    #[Route('/ors/directions', name: 'app_gpx_studio_ors_directions', methods: ['POST'])]
    public function orsDirections(Request $req): Response
    {
        $payload = json_decode($req->getContent(), true);
        if (!$payload || empty($payload['profile']) || empty($payload['coordinates'])) {
            return new JsonResponse(['error' => 'profile + coordinates fehlen'], 400);
        }

        $profile = (string)$payload['profile'];
        $coords  = $this->sanitizeCoordinates($payload['coordinates']);
        if (count($coords) < 2) {
            return new JsonResponse(['error' => 'mind. zwei gültige Koordinaten nötig'], 400);
        }

        // /v2/directions/{profile}/geojson (mit/ohne /ors-Prefix je nach Base)
        $url = $this->orsBase() . '/v2/directions/' . rawurlencode($profile) . '/geojson';

        // Default-Optionen; du kannst hier weitere ORS-Optionen durchreichen
        $body = [
            'coordinates'  => $coords,
            'instructions' => false,
            'elevation'    => false,
            'preference'   => 'recommended',
        ];

        try {
            $resp = $this->http()->request('POST', $url, ['json' => $body]);
            $status  = $resp->getStatusCode();
            $content = $resp->getContent(false);

            return new Response($content, $status, [
                'Content-Type' => 'application/json',
                'X-ORS-URL'    => $url,
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error'     => 'ORS Directions nicht erreichbar',
                'ors_url'   => $url,
                'exception' => $e->getMessage(),
            ], 502);
        }
    }

    #[Route('/ors/match', name: 'app_gpx_studio_ors_match', methods: ['POST'])]
    public function orsMatch(Request $req): Response
    {
        $payload = json_decode($req->getContent(), true);
        if (!$payload || empty($payload['profile']) || empty($payload['coordinates'])) {
            return new JsonResponse(['error' => 'profile + coordinates fehlen'], 400);
        }

        $profile = (string)$payload['profile'];
        $coords  = $this->sanitizeCoordinates($payload['coordinates']);
        if (count($coords) < 2) {
            return new JsonResponse(['error' => 'mind. zwei gültige Koordinaten nötig'], 400);
        }

        // /v2/match/{profile}/geojson
        $url = $this->orsBase() . '/v2/match/' . rawurlencode($profile) . '/geojson';

        $body = [
            'coordinates'  => $coords,
            // sinnvolle Defaults; ggf. über Payload überschreibbar
            'radiuses'     => array_fill(0, count($coords), 50), // 50m Suchradius je Punkt
            'gps_accuracy' => 15,
            // 'timestamps' => [...], // optional, falls vorhanden
        ];

        try {
            $resp = $this->http()->request('POST', $url, ['json' => $body]);
            $status  = $resp->getStatusCode();
            $content = $resp->getContent(false);

            return new Response($content, $status, [
                'Content-Type' => 'application/json',
                'X-ORS-URL'    => $url,
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error'     => 'ORS Match nicht erreichbar',
                'ors_url'   => $url,
                'exception' => $e->getMessage(),
            ], 502);
        }
    }

    #[Route('/ors/health', name: 'app_gpx_studio_ors_health', methods: ['GET'])]
    public function orsHealth(): Response
    {
        $url = $this->orsBase() . '/v2/health';
        try {
            $resp = $this->http()->request('GET', $url);
            $status  = $resp->getStatusCode();
            $content = $resp->getContent(false);

            return new Response($content, $status, [
                'Content-Type' => 'application/json',
                'X-ORS-URL'    => $url,
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error'     => 'ORS Health nicht erreichbar',
                'ors_url'   => $url,
                'exception' => $e->getMessage(),
            ], 502);
        }
    }
}
