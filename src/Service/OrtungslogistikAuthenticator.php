<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OrtungslogistikAuthenticator
{
    public function __construct(private HttpClientInterface $client) {}

    public function login(string $username, string $password): string
    {
        $response = $this->client->request('POST', 'https://www.ortungslogistik.de/Account/Login', [
            'body' => [
                'Email' => $username,
                'Password' => $password,
                'RememberMe' => 'true'
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'max_redirects' => 0,
        ]);

        $info = $response->getInfo();
        $headers = $info['response_headers'] ?? [];

        foreach ($headers as $header) {
            if (stripos($header, 'set-cookie:') === 0 && str_contains($header, 'OLFleetPortalIdentity')) {
                preg_match('/OLFleetPortalIdentity=([^;]+);/', $header, $matches);
                if (isset($matches[1])) {
                    return 'OLFleetPortalIdentity=' . urlencode($matches[1]);
                }
            }
        }

        throw new \Exception('Login fehlgeschlagen oder Cookie nicht gefunden.');
    }
}



