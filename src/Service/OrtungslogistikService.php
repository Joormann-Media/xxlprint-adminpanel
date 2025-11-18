<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OrtungslogistikService
{
    public function __construct(
        private HttpClientInterface $ortungslogistikClient,
        private OrtungslogistikAuthenticator $authenticator
    ) {}

    public function fetchLiveData(): array
    {
        $username = $_ENV['ORTUNGS_USERNAME'];
        $password = $_ENV['ORTUNGS_PASSWORD'];
        $cookie = $this->authenticator->login($username, $password);

        $groupId = 1;
        $lastTicks = '638892231116120000';
        $timestamp = (string) round(microtime(true) * 1000);

        $response = $this->ortungslogistikClient->request('GET', 'Live/Refresh', [
            'query' => [
                'groupID' => $groupId,
                'lastLocationUpdate' => $lastTicks,
                '_' => $timestamp,
            ],
            'headers' => [
                'Cookie' => $cookie,
            ],
        ]);

        return $response->toArray(false);
    }
}

