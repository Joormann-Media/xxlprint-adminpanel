<?php

namespace App\Repository;

use App\Entity\DevelopersBlog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DevelopersBlog>
 *
 * @method DevelopersBlog|null find($id, $lockMode = null, $lockVersion = null)
 * @method DevelopersBlog|null findOneBy(array $criteria, array $orderBy = null)
 * @method DevelopersBlog[]    findAll()
 * @method DevelopersBlog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DevelopersBlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DevelopersBlog::class);
    }

    /**
     * @return DevelopersBlog[] Returns published posts ordered by date desc
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.status = :status')
            ->setParameter('status', 'published')
            ->orderBy('b.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBySlug(string $slug): ?DevelopersBlog
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return DevelopersBlog[] Returns posts assigned to a project
     */
    public function findByProject(int $projectId): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.project IS NOT NULL')
            ->andWhere('b.project = :projectId')
            ->setParameter('projectId', $projectId)
            ->orderBy('b.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return DevelopersBlog[] Returns latest n blogposts regardless of status
     */
    public function findLatest(int $limit = 5): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return DevelopersBlog[] Returns blogposts filtered by tag name
     */
    public function findByTag(string $tag): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.tags', 't')
            ->andWhere('t.name = :tag')
            ->setParameter('tag', $tag)
            ->orderBy('b.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return DevelopersBlog[] Returns blogposts by status
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.status = :status')
            ->setParameter('status', $status)
            ->orderBy('b.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return DevelopersBlog[] Returns blogposts by author
     */
    public function findByAuthor(int $authorId): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.author = :authorId')
            ->setParameter('authorId', $authorId)
            ->orderBy('b.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
