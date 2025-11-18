<?php
namespace App\Controller;

use App\Entity\WebsiteSettings;
use App\Repository\WebsiteSettingsRepository;
use App\Repository\PopUpManagerRepository;
use App\Repository\VacationManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/website-settings', name: 'api_website_settings')]
final class WebsiteSettingsApiController extends AbstractController
{
    /**
     * Get the website status based on the current settings.
     * This endpoint returns different responses based on the website mode:
     * - "active": Returns a success message.
     * - "maintenance": Returns maintenance information.
     * - "popup": Returns popup information if available.
     * - "vacation": Returns vacation information if available.
     
     */
    #[Route('/', name: 'get_status_based', methods: ['GET'])]
    public function getStatusBasedResponse(
        WebsiteSettingsRepository $websiteRepo,
        PopUpManagerRepository $popupRepo,
        VacationManagerRepository $vacationRepo
    ): JsonResponse {
        // 1️⃣ Website-Einstellungen abrufen
        $settings = $websiteRepo->findOneBy([]);

        if (!$settings) {
            return new JsonResponse(['error' => 'Keine Website-Einstellungen gefunden'], 404);
        }

        // 2️⃣ Status & Datum bestimmen
        $status = $settings->getWebsiteMode();
        $currentDate = new \DateTime();
        $settingsExpire = $settings->getActiveUntil();

        // 3️⃣ Je nach Status verschiedene Antworten
        switch ($status) {
            case 'active':
                $response = new JsonResponse(['message' => 'All Systems Go'], 200);
                break;

            case 'maintenance':
                $response = new JsonResponse([
                    'message' => 'Die Website befindet sich im Wartungsmodus!',
                    'maintenance_info' => [
                        'lastUpdate' => $settings->getLastUpdate()?->format('Y-m-d H:i:s'),
                        'lastUpdateBy' => $settings->getLastUpdateBy(),
                    ]
                ], 200);
                break;

            case 'popup':
                $popupId = $settings->getWebsiteMessageId();
                $popup = $popupRepo->findOneBy(['id' => $popupId]);
                $currentDate = new \DateTime();
                $settingsExpire = $settings->getActiveUntil();

                if ($settingsExpire < $currentDate) {
                    $response = new JsonResponse(['message' => 'All Systems Go'], 200);
                }

                elseif (!$popup) {
                    $response = new JsonResponse(['message' => 'Kein PopUp für die Website-Einstellungen gefunden.'], 404);
                } else {
                    $response = new JsonResponse([
                        'message' => 'Popup-Mode aktiv!',
                        'popups' => [
                            [
                                'id' => $popup->getId(),
                                'title' => $popup->getPopupName(),
                                'content' => $popup->getPopupContent(),
                                'created_at' => $popup->getPopupCreate()->format('Y-m-d H:i:s'),
                                'created_by' => $popup->getPopupUser(),
                                'expires_at' => $popup->getPopupExpires()->format('Y-m-d H:i:s'),                                
                            ]
                        ],
                    ], 200);
                }
                break;

            case 'vacation':
                $vacations = $vacationRepo->findAll();
                $vacationData = array_map(fn($vac) => [
                    'id' => $vac->getId(),
                    'content' => $vac->getVacationContent(),
                    'user' => $vac->getVacationUser(),
                    'start_date' => $vac->getVacationStart()->format('Y-m-d'),
                    'end_date' => $vac->getVacationExpires()->format('Y-m-d'),
                ], $vacations);

                $response = new JsonResponse([
                    'message' => 'Urlaubsmodus aktiviert!',
                    'vacations' => $vacationData,
                ], 200);
                break;

            default:
                $response = new JsonResponse(['message' => 'Unbekannter Status!'], 400);
                break;
        }

        // Add CORS headers to the response
        $response->headers->set('Access-Control-Allow-Origin', 'https://xxl-print-wesel.de');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Content-Type', 'application/json'); // Ensure JSON content type

        return $response;
    }

    /**
     * Handle preflight requests for CORS.
     */
    #[Route('/{any}', name: 'cors_preflight', methods: ['OPTIONS'], requirements: ['any' => '.*'])]
    public function preflight(): Response
    {
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', 'https://xxl-print-wesel.de');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    #[Route('/update', name: 'update_website_settings', methods: ['PUT'])]
    public function updateWebsiteSettings(
        Request $request,
        WebsiteSettingsRepository $websiteRepo,
        PopUpManagerRepository $popupRepo,
        EntityManagerInterface $em
    ): JsonResponse {
        $settings = $websiteRepo->findOneBy([]);

        if (!$settings) {
            return new JsonResponse(['error' => 'Keine Website-Einstellungen gefunden'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Ungültiges JSON'], 400);
        }

        try {
            if (isset($data['websiteMode'])) {
                $settings->setWebsiteMode($data['websiteMode']);
            }

            if (isset($data['lastUpdate'])) {
                $settings->setLastUpdate(new \DateTime($data['lastUpdate']));
            }

            if (isset($data['lastUpdateBy'])) {
                $settings->setLastUpdateBy($data['lastUpdateBy']);
            }

            if (array_key_exists('activeUntil', $data)) {
                $settings->setActiveUntil($data['activeUntil'] ? new \DateTime($data['activeUntil']) : null);
            }

            if (array_key_exists('websiteMessageId', $data)) {
                if ($data['websiteMessageId'] === null) {
                    $settings->setWebsiteMessageId(null);
                } else {
                    $popup = $popupRepo->find($data['websiteMessageId']);
                    if (!$popup) {
                        return new JsonResponse(['error' => 'Ungültige PopUpManager-ID'], 400);
                    }
                    $settings->setWebsiteMessageId($popup); // Ensure this sets the PopUpManager object
                }
            }

            $em->persist($settings); // Ensure the entity is marked for persistence
            $em->flush(); // Persist changes to the database

            return new JsonResponse(['message' => 'Website-Einstellungen erfolgreich aktualisiert.']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Fehler beim Aktualisieren: ' . $e->getMessage()], 500);
        }
    }
}
