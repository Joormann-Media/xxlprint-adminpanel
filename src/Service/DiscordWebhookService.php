<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordWebhookService
{
    // private string $webhookUrl is declared below; set its value via constructor
    private string $webhookUrl;
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient, string $webhookUrl)
    {
        $this->httpClient = $httpClient;
        $this->webhookUrl = $webhookUrl;
    }

    public function send(string $content, array $options = []): bool
    {
        $payload = array_merge(['content' => $content], $options);

        try {
            $response = $this->httpClient->request('POST', $this->webhookUrl, [
                'json' => $payload,
            ]);
            return $response->getStatusCode() === 204;
        } catch (\Throwable $e) {
            // Log das irgendwo, wenn du willst
            return false;
        }
    }
}
