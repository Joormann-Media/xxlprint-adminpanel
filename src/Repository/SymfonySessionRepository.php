<?php

namespace App\Repository;

use App\Entity\SymfonySession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SymfonySessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SymfonySession::class);
    }

    public function findActiveSessions(): array
    {
        $now = time();

        return $this->createQueryBuilder('s')
            ->where('s.sessTime + s.sessLifetime > :now')
            ->setParameter('now', $now)
            ->orderBy('s.sessTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function deleteBySessId(string $sessId): int
    {
        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.sessId = :id')
            ->setParameter('id', $sessId)
            ->getQuery()
            ->execute();
    }

    public function deleteExpiredSessions(): int
    {
        $now = time();

        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.sessTime + s.sessLifetime < :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->execute();
    }

    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.userId = :uid')
            ->setParameter('uid', $userId)
            ->orderBy('s.sessTime', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
