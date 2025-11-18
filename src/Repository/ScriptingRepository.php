<?php

namespace App\Repository;

use App\Entity\Scripting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Scripting>
 *
 * @method Scripting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Scripting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Scripting[]    findAll()
 * @method Scripting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScriptingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scripting::class);
    }
}

