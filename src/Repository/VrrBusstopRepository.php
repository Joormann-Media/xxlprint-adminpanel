<?php

namespace App\Repository;

use App\Entity\VrrBusstop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<VrrBusstop>
 */
class VrrBusstopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VrrBusstop::class);
    }
public function findWithPagination(int $page = 1, int $limit = 25): array
{
    $offset = ($page - 1) * $limit;

    $qb = $this->createQueryBuilder('v')
        ->orderBy('v.stopNr', 'ASC')
        ->setFirstResult($offset)
        ->setMaxResults($limit);

    return $qb->getQuery()->getResult();
}

public function countAll(): int
{
    return (int) $this->createQueryBuilder('v')
        ->select('COUNT(v)')
        ->getQuery()
        ->getSingleScalarResult();
}
public function searchWithPagination(?string $search, int $page, int $limit): array
{
    $qb = $this->createQueryBuilder('v');

    if ($search) {
        $searchTerm = '%' . mb_strtolower($search) . '%';

        $fields = [
            'stopNr', 'version', 'stopType', 'stopName', 'stopNameWoLocality', 'stopShortName',
            'stopPosX', 'stopPosY', 'place', 'occ',
            'fareZone1Nr', 'fareZone2Nr', 'fareZone3Nr', 'fareZone4Nr', 'fareZone5Nr', 'fareZone6Nr',
            'globalId', 'validFrom', 'validTo',
            'placeId', 'gisMotFlag',
            'isCentralStop', 'isResponsibleStop',
            'interchangeType', 'interchangeQuality'
        ];

        $orX = $qb->expr()->orX();
        foreach ($fields as $field) {
            $orX->add("LOWER(CONCAT('', v.$field)) LIKE :search");
        }
        $qb->andWhere($orX)
           ->setParameter('search', $searchTerm);
    }

    $qb->orderBy('v.stopNr', 'ASC')
       ->setFirstResult(($page - 1) * $limit)
       ->setMaxResults($limit);

    $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($qb);

    return [
        'results' => iterator_to_array($paginator),
        'total' => count($paginator),
    ];
}




    //    /**
    //     * @return VrrBusstop[] Returns an array of VrrBusstop objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?VrrBusstop
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
