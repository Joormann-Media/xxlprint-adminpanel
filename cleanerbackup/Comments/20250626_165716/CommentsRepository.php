<?php

namespace App\Repository;

use App\Entity\Comments; // Updated entity name
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comments> // Updated entity name
 */
class CommentsRepository extends ServiceEntityRepository // Updated repository name
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class); // Updated entity name
    }

    /**
     * Alle Kommentare sortiert nach Datum (neueste zuerst)
     */
    public function findAllOrderedByDateDesc(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.commentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Kommentare für einen bestimmten Beitrag (Post-ID)
     */
    public function findByPostId(int $postId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.postId = :postId')
            ->setParameter('postId', $postId)
            ->orderBy('c.commentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Kommentare nach Sichtbarkeit (z. B. 'public', 'private', ...)
     */
    public function findByVisibility(string $visibility): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.commentVisibility = :visibility')
            ->setParameter('visibility', $visibility)
            ->orderBy('c.commentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Kommentare eines bestimmten Users
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.commentUser', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('c.commentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Kommentare eines Users für einen bestimmten Post
     */
    public function findByUserAndPost(int $userId, int $postId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.commentUser', 'u')
            ->andWhere('u.id = :userId')
            ->andWhere('c.postId = :postId')
            ->setParameter('userId', $userId)
            ->setParameter('postId', $postId)
            ->orderBy('c.commentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Kommentare nach Zeitraum (z. B. letzte 7 Tage)
     */
    public function findRecentComments(\DateTimeInterface $since): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.commentDate >= :since')
            ->setParameter('since', $since)
            ->orderBy('c.commentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Alle Sichtbaren Kommentare für Frontend (ohne private/internal/hidden)
     */
    public function findPublicComments(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.commentVisibility = :visibility')
            ->setParameter('visibility', 'public')
            ->orderBy('c.commentDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
