<?php

namespace App\Repository;

use App\Entity\HelpEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HelpEntry>
 */
class HelpEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HelpEntry::class);
    }

    // Optional: eigene Finder/Query-Methoden kannst du hier ergänzen
}

