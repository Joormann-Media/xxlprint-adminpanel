<?php

namespace App\Repository;

use App\Entity\RumfassWisdom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RumfassWisdom>
 */
class RumfassWisdomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RumfassWisdom::class);
    }

    /**
     * Finde alle aktiven Weisheiten, optional mit Limit.
     */
    public function findActive(int $limit = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.active = :active')
            ->setParameter('active', true)
            ->orderBy('r.createdAt', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Finde zufällige aktive Weisheit.
     */
public function findRandomActive(): ?RumfassWisdom
{
    $conn = $this->getEntityManager()->getConnection();
    $sql = 'SELECT id FROM rumfass_wisdoms WHERE active = true ORDER BY RAND() LIMIT 1';

    $result = $conn->executeQuery($sql);
    $data = $result->fetchAssociative();

    if (!$data || !isset($data['id'])) {
        return null;
    }

    return $this->find($data['id']);
}



    /**
     * Suche in Weisheiten nach Text.
     */
    public function searchByContent(string $term): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.content LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Finde Weisheiten nach Datum (z. B. für Archiv).
     */
    public function findByDateRange(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from->format('Y-m-d 00:00:00'))
            ->setParameter('to', $to->format('Y-m-d 23:59:59'))
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
