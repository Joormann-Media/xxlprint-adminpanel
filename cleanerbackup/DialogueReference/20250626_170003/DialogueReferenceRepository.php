<?php

namespace App\Repository;

use App\Entity\DialogueReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DialogueReference>
 *
 * @method DialogueReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method DialogueReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method DialogueReference[]    findAll()
 * @method DialogueReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DialogueReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DialogueReference::class);
    }

    // Beispiel für eine eigene Query:
    /**
     * @return DialogueReference[] Returns an array of DialogueReference objects für einen bestimmten Raum
     */
    public function findByRoomId(string $roomId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.roomId = :roomId')
            ->setParameter('roomId', $roomId)
            ->orderBy('d.dialogId', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Noch mehr Piraten-Methoden? Einfach hier anlegen!
}