<?php

// src/Service/SmsSenderService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsSenderService
{
    public function __construct(
        private HttpClientInterface $client,
        private string $apiToken,
        private string $baseUrl,
        private string $defaultSender
    ) {}

    public function sendSms(string $recipient, string $message): array
    {
        $url = $this->baseUrl . '/sms';

        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'to' => $recipient,
                'text' => $message,
                'from' => $this->defaultSender,
                'json' => true,
            ],
        ]);

        $data = $response->toArray(false);

        if (!isset($data['success']) || $data['success'] !== '100') {
            $error = $data['messages'][0]['error_text'] ?? 'Unbekannter Fehler';
            throw new \RuntimeException('SMS konnte nicht gesendet werden: ' . $error);
        }

        return $data;
    }
}
