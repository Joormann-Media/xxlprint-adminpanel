<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\WebhookService;

class WebhookReceiverController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private WebhookService $webhookService
    ) {}

    #[Route('/webhook/ping', name: 'webhook_ping', methods: ['POST'])]
    public function receive(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || empty($data['ip']) || empty($data['message'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }

        $this->logger->info('Webhook empfangen', $data);
        
        $this->webhookService->handlePing($data);

        return $this->json(['status' => 'received']);
    }
}