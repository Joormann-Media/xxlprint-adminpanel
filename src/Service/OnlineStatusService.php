<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Predis\Client;

class OnlineStatusService
{
    private Client $redis;
    private const PREFIX = 'online_user:';
    private const TTL = 300; // 5 Minuten Inaktivitäts-Timeout

    public function __construct(private EntityManagerInterface $em)
    {
        $this->redis = new Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
            'password' => 'DjTmKJc310810090210',
        ]);
    }

    public function markOnline(User $user): void
    {
        $key = self::PREFIX . $user->getId();
        $this->redis->setex($key, self::TTL, time());
    }

    public function isOnline(User $user): bool
    {
        return $this->redis->exists(self::PREFIX . $user->getId()) > 0;
    }

    public function getAllOnlineUserIds(): array
    {
        $keys = $this->redis->keys(self::PREFIX . '*');
        return array_map(
            fn(string $key) => (int) str_replace(self::PREFIX, '', $key),
            $keys
        );
    }
}
