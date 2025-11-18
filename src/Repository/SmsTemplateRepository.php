<?php

namespace App\Repository;

use App\Entity\SmsTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SmsTemplate>
 *
 * @method SmsTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsTemplate[]    findAll()
 * @method SmsTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsTemplate::class);
    }

    // Optional: benutzerdefinierte Methoden

    public function findLatest(int $limit = 10): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
