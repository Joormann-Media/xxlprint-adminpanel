<?php

namespace App\Repository;

use App\Entity\DashboardModules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DashboardModules>
 *
 * @method DashboardModules|null find($id, $lockMode = null, $lockVersion = null)
 * @method DashboardModules|null findOneBy(array $criteria, array $orderBy = null)
 * @method DashboardModules[]    findAll()
 * @method DashboardModules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DashboardModulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashboardModules::class);
    }

    public function save(DashboardModules $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(DashboardModules $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
