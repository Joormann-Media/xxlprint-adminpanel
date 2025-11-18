<?php

namespace App\Controller\Api;

use App\Entity\DiscordWebhookHistory;
use App\Repository\DiscordWebhookHistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/discord-webhook-history', name: 'api_discord_webhook_history_')]
final class DiscordWebhookHistoryApiController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(DiscordWebhookHistoryRepository $historyRepository): JsonResponse
    {
        $histories = $historyRepository->findBy([], ['timestamp' => 'DESC']); // neuste zuerst

        $sanitize = fn($v) => ($v === null || $v === '') ? 'FOLGT' : $v;

        $data = array_map(function(DiscordWebhookHistory $history) use ($sanitize) {
            return [
                'id'        => $history->getId(),
                'timestamp' => $history->getTimestamp()?->format('Y-m-d H:i:s') ?? 'FOLGT',
                'username'  => $history->getUsername()?->getUserIdentifier() ?? 'FOLGT', // Username oder Email
                'hooktext'  => $sanitize($history->getHooktext()),
                'hookstatus'=> $sanitize($history->getHookstatus()),
            ];
        }, $histories);

        return new JsonResponse($data, 200);
    }

    #[Route('/{id<\d+>}', name: 'detail', methods: ['GET'])]
    public function detail(?DiscordWebhookHistory $history = null): JsonResponse
    {
        $sanitize = fn($v) => ($v === null || $v === '') ? 'FOLGT' : $v;

        if (!$history) {
            return new JsonResponse(['error' => 'Kein Eintrag gefunden'], 404);
        }

        $data = [
            'id'        => $history->getId(),
            'timestamp' => $history->getTimestamp()?->format('Y-m-d H:i:s') ?? 'FOLGT',
            'username'  => $history->getUsername()?->getUserIdentifier() ?? 'FOLGT',
            'hooktext'  => $sanitize($history->getHooktext()),
            'hookstatus'=> $sanitize($history->getHookstatus()),
        ];

        return new JsonResponse($data, 200);
    }
}
