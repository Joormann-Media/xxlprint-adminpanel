<?php

namespace App\Repository;

use App\Entity\UserDeviceLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserDeviceLog>
 */
class UserDeviceLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDeviceLog::class);
    }

    // Optional: Eigene Log-Abfragen hier einbauen
}
