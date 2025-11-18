<?php

namespace App\Repository;

use App\Entity\UserSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<UserSession>
 *
 * @method UserSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserSession[]    findAll()
 * @method UserSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $em)
    {
        parent::__construct($registry, UserSession::class);
    }

    public function save(UserSession $session, bool $flush = false): void
{
    $em = $this->getEntityManager();
    $em->persist($session);

    if ($flush) {
        $em->flush();
    }
}

public function remove(UserSession $session, bool $flush = false): void
{
    $em = $this->getEntityManager();
    $em->remove($session);

    if ($flush) {
        $em->flush();
    }
}


    /**
     * Alle aktiven Sessions für einen bestimmten User (optional sortiert)
     */
    public function findActiveByUser(int $userId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :userId')
            ->andWhere('s.isActive = true')
            ->setParameter('userId', $userId)
            ->orderBy('s.lastActiveAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Alle aktuell aktiven Sessions
     */
    public function findActiveSessions(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isActive = true')
            ->orderBy('s.lastActiveAt', 'DESC')
            ->getQuery()
            ->getResult();
    }



    /**
     * Einzelne Session anhand der Session-ID finden
     */
    public function findBySessionId(string $sessionId): ?UserSession
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.sessionId = :sid')
            ->setParameter('sid', $sessionId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deleteByUserId(int $userId): int
    {
        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }
    
    public function deleteBySessionId(string $sessionId): int
    {
        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.sessionId = :sid')
            ->setParameter('sid', $sessionId)
            ->getQuery()
            ->execute();
    }
    public function deactivateAllSessionsForUser(int $userId): void
{
    $this->createQueryBuilder('s')
        ->update()
        ->set('s.isActive', ':false')
        ->where('s.user = :userId')
        ->setParameter('false', false)
        ->setParameter('userId', $userId)
        ->getQuery()
        ->execute();
}
    public function findExpiredSessions(?\DateTime $olderThan = null): array
{
    $qb = $this->createQueryBuilder('s')
        ->where('s.isActive = false');

    if ($olderThan) {
        $qb->andWhere('s.lastActiveAt < :cutoff')
           ->setParameter('cutoff', $olderThan);
    }

    return $qb->getQuery()->getResult();
}

public function deleteExpiredSessions(?\DateTimeInterface $olderThan = null): int
    {
        $olderThan ??= new \DateTime('-4 hours');

        return $this->createQueryBuilder('s')
            ->delete()
            ->andWhere('s.lastActiveAt < :cutoff OR s.isActive = false')
            ->setParameter('cutoff', $olderThan)
            ->getQuery()
            ->execute();
    }

    public function findRecentSessions(int $limit = 20, bool $trustedOnly = false): array
{
    $qb = $this->createQueryBuilder('s')
        ->leftJoin('s.user', 'u')->addSelect('u')
        ->orderBy('s.lastActiveAt', 'DESC')
        ->setMaxResults($limit);

    if ($trustedOnly) {
        $qb->andWhere('s.isTrusted = true');
    }

    return $qb->getQuery()->getResult();
}

}
