<?php

namespace App\Repository;

use App\Entity\Attachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attachment::class);
    }

    public function findAllByMessage(int $messageId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.message = :msgId')
            ->setParameter('msgId', $messageId)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
