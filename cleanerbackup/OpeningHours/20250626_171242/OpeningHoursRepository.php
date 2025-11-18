<?php

namespace App\Repository;

use App\Entity\OpeningHours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OpeningHours|null find($id, $lockMode = null, $lockVersion = null)
 * @method OpeningHours|null findOneBy(array $criteria, array $orderBy = null)
 * @method OpeningHours[]    findAll()
 * @method OpeningHours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpeningHoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpeningHours::class);
    }

    // Beispiel: Finde alle Öffnungszeiten für einen bestimmten Tag
    public function findByDay(string $day): ?OpeningHours
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.day = :day')
            ->setParameter('day', $day)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Beispiel: Finde alle Öffnungszeiten in einem bestimmten Zeitraum
    public function findByTimeRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.morningStart BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }
}
