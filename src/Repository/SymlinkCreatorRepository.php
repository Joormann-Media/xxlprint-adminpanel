<?php

namespace App\Repository;

use App\Entity\SymlinkCreator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SymlinkCreator>
 *
 * @method SymlinkCreator|null find($id, $lockMode = null, $lockVersion = null)
 * @method SymlinkCreator|null findOneBy(array $criteria, array $orderBy = null)
 * @method SymlinkCreator[]    findAll()
 * @method SymlinkCreator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SymlinkCreatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SymlinkCreator::class);
    }
}
