<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercurePublisher
{
    public function __construct(private readonly HubInterface $hub) {}

    public function sendToUser(int $userId, array $payload): void
    {
        $update = new Update(
            sprintf('/messages/%d', $userId),
            json_encode($payload)
        );

        $this->hub->publish($update);
    }
}
