<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    // Beispiel: Alle Fahrer zurückgeben
    public function findAllDrivers(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isDriver = :isDriver')
            ->setParameter('isDriver', true)
            ->orderBy('e.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Beispiel: Mitarbeiter, die aktuell beschäftigt sind
    public function findActiveEmployees(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.leftAt IS NULL')
            ->orderBy('e.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Beispiel: Nach Lizenzklasse filtern
    public function findByLicenseClass(string $licenseClass): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.licenseClass = :class')
            ->setParameter('class', $licenseClass)
            ->orderBy('e.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // ... hier kannst du beliebig weitere Query-Methoden einbauen!
}
