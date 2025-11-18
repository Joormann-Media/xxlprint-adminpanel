<?php

namespace App\Repository;

use App\Entity\PostalCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostalCode>
 */
class PostalCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostalCode::class);
    }

    //    /**
    //     * @return PostalCode[] Returns an array of PostalCode objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PostalCode
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findDistinctCitiesAndPostcodesMatching(string $query): array
{
    $qb = $this->createQueryBuilder('p');

    $qb->select('p.city, p.postcode')
        ->where('p.city LIKE :q OR p.postcode LIKE :q')
        ->setParameter('q', $query . '%');

    $rows = $qb->getQuery()->getArrayResult();

    $grouped = [];

    foreach ($rows as $row) {
        $city = $row['city'];
        $postcode = $row['postcode'];

        if (!$city || !$postcode) {
            continue;
        }

        if (!isset($grouped[$city])) {
            $grouped[$city] = [];
        }

        if (!in_array($postcode, $grouped[$city], true)) {
            $grouped[$city][] = $postcode;
        }
    }

    $result = [];

    foreach ($grouped as $city => $postcodes) {
        $result[] = [
            'city' => $city,
            'postcodes' => $postcodes,
        ];
    }

    return $result;
}

}
