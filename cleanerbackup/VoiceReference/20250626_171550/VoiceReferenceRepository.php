<?php

namespace App\Repository;

use App\Entity\VoiceReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VoiceReference>
 *
 * @method VoiceReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method VoiceReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method VoiceReference[]    findAll()
 * @method VoiceReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoiceReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VoiceReference::class);
    }
}

