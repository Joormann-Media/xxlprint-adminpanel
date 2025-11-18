<?php

namespace App\Repository;

use App\Entity\SoundReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SoundReference>
 */
class SoundReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SoundReference::class);
    }

    /**
     * @return SoundReference[] Returns an array of SoundReference objects
     */
    public function findByRoomName(string $room): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.roomName = :room')
            ->setParameter('room', $room)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
