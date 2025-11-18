<?php
namespace App\Repository;

use App\Entity\EMailSignature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EMailSignatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EMailSignature::class);
    }

    // eigene Abfragen hier ergänzen
}
