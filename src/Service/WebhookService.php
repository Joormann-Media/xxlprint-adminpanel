<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WebhookService
{
    private string $webhookUrl;
    private ?LoggerInterface $logger;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        string $webhookUrl = '',
        ?LoggerInterface $logger = null    // <-- EXPLIZIT nullable!
    ) {
        $this->webhookUrl = $webhookUrl;
        $this->logger = $logger;
    }

    /**
     * Sende einen eingebetteten Webhook an Discord (mit Titel, Feldern etc.)
     */
    public function send(string $title, array $payload = [], array $options = []): bool
    {
        if (!$this->webhookUrl) {
            return false;
        }

        $data = [
            'username' => $options['username'] ?? 'SecurityBot',
            'avatar_url' => $options['avatar_url'] ?? null,
            'embeds' => [[
                'title' => $title,
                'color' => $options['color'] ?? 16711680, // rot
                'fields' => array_map(fn($k, $v) => [
                    'name' => ucfirst((string) $k),
                    'value' => (string) $v,
                    'inline' => true,
                ], array_keys($payload), $payload),
                'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
            ]]
        ];

        try {
            $this->httpClient->request('POST', $this->webhookUrl, ['json' => $data]);
            return true;
        } catch (\Throwable $e) {
            if ($this->logger) {
                $this->logger->error('[WebhookService] Fehler beim Senden des Webhooks: ' . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Sende manuell ein beliebiges Payload (komplettes JSON)
     */
    public function sendRaw(array $payload): bool
    {
        if (!$this->webhookUrl) {
            return false;
        }

        try {
            $response = $this->httpClient->request('POST', $this->webhookUrl, [
                'json' => $payload,
            ]);

            $status = $response->getStatusCode();
            $content = $response->getContent(false); // keine Exception bei 4xx!

            if ($status >= 200 && $status < 300) {
                return true;
            }

            if ($this->logger) {
                $this->logger->error("[WebhookService:sendRaw] HTTP $status → $content");
            }

            return false;
        } catch (\Throwable $e) {
            if ($this->logger) {
                $this->logger->error('[WebhookService:sendRaw] Fehler: ' . $e->getMessage());
            }
            return false;
        }
    }
}
