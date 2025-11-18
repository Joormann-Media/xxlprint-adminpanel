<?php

namespace App\Repository;

use App\Entity\Release;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReleaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Release::class);
    }

    // Beispiel: Neueste Version einer Software abrufen
    public function findLatestReleaseBySoftwareId(string $softwareId): ?Release
    {
        return $this->createQueryBuilder('r')
            ->where('r.softwareId = :id')
            ->setParameter('id', $softwareId)
            ->orderBy('r.releaseDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
