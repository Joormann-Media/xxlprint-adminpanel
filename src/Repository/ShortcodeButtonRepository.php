<?php

namespace App\Repository;

use App\Entity\ShortcodeButton;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShortcodeButton>
 *
 * @method ShortcodeButton|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortcodeButton|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortcodeButton[]    findAll()
 * @method ShortcodeButton[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortcodeButtonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortcodeButton::class);
    }

    // Add custom repository methods here if needed
}
