<?php

namespace App\Repository;

use App\Entity\Calendar;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Calendar>
 */
class CalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendar::class);
    }

    /**
     * Finde alle Kalender eines Benutzers (via Username)
     */
    public function findByOwnerUsername(string $username): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.owner', 'o')
            ->where('o.username = :username')
            ->setParameter('username', $username)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
