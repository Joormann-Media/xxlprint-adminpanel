<?php

// src/Repository/PopUpCategoryRepository.php

namespace App\Repository;

use App\Entity\PopUpCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PopUpCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PopUpCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PopUpCategory[]    findAll()
 * @method PopUpCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PopUpCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PopUpCategory::class);
    }

    // Beispiel für eine benutzerdefinierte Abfrage: Alle Kategorien nach Erstellungsdatum sortiert
    public function findAllOrderedByDate()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.erstelltAm', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Hier können weitere benutzerdefinierte Methoden hinzugefügt werden
}


