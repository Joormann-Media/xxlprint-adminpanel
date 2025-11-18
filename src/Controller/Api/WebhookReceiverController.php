<?php

// src/Controller/Api/WebhookReceiverController.php

namespace App\Controller\Api;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebhookReceiverController extends AbstractController
{
    #[Route('/api/webhook/{channel}', name: 'api_webhook_receive', methods: ['POST'])]
    public function receive(Request $request, LoggerInterface $logger, string $channel): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        // 🛡️ Optional: Secret/Token prüfen
        $token = $request->headers->get('X-Hook-Token');
        if ($token !== $_ENV['INTERNAL_HOOK_TOKEN']) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // 📝 Logge oder speichere den Payload
        $logger->info("📩 Webhook empfangen für Channel '{$channel}'", $payload);

        // 🔁 Optional: Trigger-Events, Datenbank, Notification etc.

        return $this->json(['status' => 'ok']);
    }
}

