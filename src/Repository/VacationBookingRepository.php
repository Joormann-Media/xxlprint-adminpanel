<?php

namespace App\Repository;

use App\Entity\VacationBooking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VacationBooking>
 */
class VacationBookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VacationBooking::class);
    }

    // Beispiel: Alle Buchungen für einen bestimmten Jahresurlaub laden
    public function findByEmployeeVacation($employeeVacation)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.employeeVacation = :ev')
            ->setParameter('ev', $employeeVacation)
            ->orderBy('b.dateTaken', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
