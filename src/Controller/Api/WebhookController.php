<?php

// src/Controller/Api/WebhookController.php
namespace App\Controller\Api;

use App\Service\WebhookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/webhook')]
class WebhookController extends AbstractController
{
    #[Route('/test', name: 'api_webhook_test', methods: ['POST'])]
    public function test(Request $request, WebhookService $webhook): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // 👇 Test-Logik für Discord-Webhook
        $webhook->send('🔔 Test empfangen', $data);

        return $this->json(['status' => 'ok']);
    }
}
