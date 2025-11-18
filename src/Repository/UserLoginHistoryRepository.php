<?php

namespace App\Repository;

use App\Entity\UserLoginHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserLoginHistory>
 *
 * @method UserLoginHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLoginHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLoginHistory[]    findAll()
 * @method UserLoginHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLoginHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLoginHistory::class);
    }

    public function save(UserLoginHistory $entry, bool $flush = false): void
    {
        $this->_em->persist($entry);

        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(UserLoginHistory $entry, bool $flush = false): void
    {
        $this->_em->remove($entry);

        if ($flush) {
            $this->_em->flush();
        }
    }

    // Beispiel: Letzte Logins eines Users abrufen
    public function findLatestForUser(int $userId, int $limit = 10): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('h.loginAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
