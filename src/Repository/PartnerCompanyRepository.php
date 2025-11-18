<?php

namespace App\Repository;

use App\Entity\PartnerCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PartnerCompany>
 *
 * @method PartnerCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method PartnerCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method PartnerCompany[]    findAll()
 * @method PartnerCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerCompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerCompany::class);
    }

    // 🔍 Beispiel: Alle aktiven Partner nach Stadt sortiert
    public function findActiveByCity(string $city): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.isActive = :active')
            ->andWhere('p.city = :city')
            ->setParameter('active', true)
            ->setParameter('city', $city)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // 🔍 Optional: Partner anhand Slug finden
    public function findOneBySlug(string $slug): ?PartnerCompany
    {
        return $this->findOneBy(['slug' => $slug]);
    }
}

