<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class SearchController extends AbstractController
{
    #[Route('/admin/search', name: 'admin_search', methods: ['GET'])]
    public function search(Request $request, RouterInterface $router): JsonResponse
    {
        $query = trim($request->query->get('q', ''));
        $results = [];

        if ($query !== '') {
            // 1. Symfony Routen durchsuchen
            foreach ($router->getRouteCollection()->all() as $name => $route) {
                if (stripos($name, $query) !== false) {
                    // Routen mit _ (z.B. _profiler) ausfiltern? Hier optional.
                    if (strpos($name, '_') === 0) continue;
                    $url = null;
                    try {
                        $url = $this->generateUrl($name);
                    } catch (\Exception $e) {
                        $url = null;
                    }
                    $results[] = [
                        'type' => 'Route',
                        'name' => $name,
                        'url' => $url,
                    ];
                }
            }

            // 2. Statische Module-Liste (optional, ergänz nach Bedarf)
            $modules = [
                ['name' => 'Dashboard', 'url' => '/admin'],
                ['name' => 'Audio Manager', 'url' => '/admin/audio_asset'],
                ['name' => 'Backgrounds', 'url' => '/admin/background_asset'],
                ['name' => 'User', 'url' => '/admin/user'],
                ['name' => 'Settings', 'url' => '/admin/settings'],
                // ... hier deine weiteren Apps/Module ergänzen!
            ];
            foreach ($modules as $mod) {
                if (stripos($mod['name'], $query) !== false) {
                    $results[] = [
                        'type' => 'Module',
                        'name' => $mod['name'],
                        'url' => $mod['url'],
                    ];
                }
            }
        }

        // Optional: Duplikate entfernen
        $results = array_unique($results, SORT_REGULAR);

        // Nach Typ sortieren, wenn du willst
        // usort($results, fn($a, $b) => strcmp($a['type'], $b['type']));

        return $this->json($results);
    }
}
