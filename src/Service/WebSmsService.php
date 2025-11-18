<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WebSmsService
{
    private string $username;
    private string $password;
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client, string $username, string $password)
    {
        $this->client = $client;
        $this->username = $username;
        $this->password = $password;
    }

    public function sendSms(string $recipientNumber, string $message): bool
    {
        $url = 'https://api.websms.com/rest/smsmessaging/simple';

        $payload = [
            'recipientAddressList' => [$recipientNumber],
            'messageContent' => ['messageText' => $message],
            'senderAddress' => 'Joormann',
        ];

        $response = $this->client->request('POST', $url, [
            'auth_basic' => [$this->username, $this->password],
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $payload,
        ]);

        return $response->getStatusCode() === 200;
    }
}

