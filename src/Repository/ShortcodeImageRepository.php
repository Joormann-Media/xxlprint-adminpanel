<?php

namespace App\Repository;

use App\Entity\ShortcodeImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShortcodeImage>
 *
 * @method ShortcodeImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortcodeImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortcodeImage[]    findAll()
 * @method ShortcodeImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortcodeImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortcodeImage::class);
    }

    /**
     * @return ShortcodeImage[]
     */
    public function findActiveSorted(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('s.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByTag(string $tag): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.tag = :tag')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getResult();
    }

    public function findOneByTag(string $tag): ?ShortcodeImage
    {
        return $this->findOneBy(['tag' => $tag, 'isActive' => true]);
    }
}
