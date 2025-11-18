<?php

namespace App\Repository;

use App\Entity\UserDashboardConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserDashboardConfig>
 *
 * @method UserDashboardConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDashboardConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDashboardConfig[]    findAll()
 * @method UserDashboardConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDashboardConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDashboardConfig::class);
    }

    public function save(UserDashboardConfig $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserDashboardConfig $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


}
