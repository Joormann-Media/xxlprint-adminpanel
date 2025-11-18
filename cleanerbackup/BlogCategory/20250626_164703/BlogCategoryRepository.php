<?php

namespace App\Repository;

use App\Entity\BlogCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<BlogCategory>
 */
class BlogCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogCategory::class);
    }

    /**
     * Finde Kategorien nach Name oder Slug (optional fuzzy)
     */
    public function findByNameOrSlug(string $searchTerm, bool $exact = false): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($exact) {
            $qb->where('c.name = :term')
               ->orWhere('c.slug = :term');
        } else {
            $qb->where('c.name LIKE :term')
               ->orWhere('c.slug LIKE :term');
            $searchTerm = '%' . $searchTerm . '%';
        }

        return $qb
            ->setParameter('term', $searchTerm)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Alle Kategorien mit Anzahl der Beiträge
     */
    public function findAllWithPostCount(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.posts', 'p')
            ->addSelect('COUNT(p.id) AS HIDDEN postCount')
            ->groupBy('c.id')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Sortiert nach Anzahl Beiträge
     */
    public function findAllSortedByPostCount(string $direction = 'DESC'): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.posts', 'p')
            ->addSelect('COUNT(p.id) AS HIDDEN postCount')
            ->groupBy('c.id')
            ->orderBy('postCount', $direction)
            ->getQuery()
            ->getResult();
    }

    /**
     * Hole alle Slugs für Autovervollständigung oder Validierung
     */
    public function getAllSlugs(): array
    {
        return array_column(
            $this->createQueryBuilder('c')
                ->select('c.slug')
                ->getQuery()
                ->getArrayResult(),
            'slug'
        );
    }

    /**
     * Gibt den QueryBuilder zurück (z.B. für Paginierung)
     */
    public function getQueryBuilderForAll(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.posts', 'p')
            ->addSelect('p');
    }
}
