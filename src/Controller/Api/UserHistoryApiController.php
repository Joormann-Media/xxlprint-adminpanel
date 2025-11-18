<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\UserHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/history', name: 'api_history_')]
class UserHistoryApiController extends AbstractController
{
    #[Route('/log', name: 'log', methods: ['POST'])]
    public function log(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Ungültige JSON-Daten'], 400);
        }

        if (empty($data['userId']) || empty($data['action'])) {
            return new JsonResponse(['error' => 'userId und action sind erforderlich'], 400);
        }

        $user = $em->getRepository(User::class)->find($data['userId']);

        if (!$user) {
            return new JsonResponse(['error' => 'Benutzer nicht gefunden'], 404);
        }

        $history = new UserHistory();
        $history->setUser($user);
        $history->setAction($data['action']);
        $history->setTimestamp(new \DateTime());
        $history->setIpAddress($data['ipAddress'] ?? $request->getClientIp());
        $history->setDevice($data['device'] ?? null);
        $history->setBrowserFingerprint($data['browserFingerprint'] ?? null);
        $history->setMetaData($data['metaData'] ?? null);

        $em->persist($history);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'id' => $history->getId()
        ]);
    }
}
