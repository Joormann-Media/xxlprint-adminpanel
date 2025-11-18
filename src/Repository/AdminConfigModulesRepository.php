<?php

namespace App\Repository;

use App\Entity\AdminConfigModules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdminConfigModules>
 *
 * @method AdminConfigModules|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminConfigModules|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminConfigModules[]    findAll()
 * @method AdminConfigModules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminConfigModulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminConfigModules::class);
    }
}
