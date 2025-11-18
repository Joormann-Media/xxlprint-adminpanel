<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    /**
     * Gibt alle Konversationen eines Users sortiert nach letzter Nachricht zurück
     */
    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.participants', 'p')
            ->addSelect('p')
            ->leftJoin('c.messages', 'm')
            ->addSelect('m')
            ->where(':user MEMBER OF c.participants')
            ->setParameter('user', $user)
            ->orderBy('c.lastMessageAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Gibt eine 1:1-Konversation zwischen zwei Benutzern zurück (falls vorhanden)
     */
    public function findOneBetweenTwoUsers(User $userA, User $userB): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->where('c.isGroup = false')
            ->andWhere(':userA MEMBER OF c.participants')
            ->andWhere(':userB MEMBER OF c.participants')
            ->setParameter('userA', $userA)
            ->setParameter('userB', $userB)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Gibt die Anzahl ungelesener Nachrichten in allen Konversationen eines Benutzers zurück
     */
    public function countUnreadMessagesForUser(User $user): int
    {
        return $this->_em->createQuery(
            'SELECT COUNT(m.id)
             FROM App\Entity\Message m
             WHERE m.recipient = :user
             AND m.isRead = false'
        )
        ->setParameter('user', $user)
        ->getSingleScalarResult();
    }
    public function findExistingConversation(User $a, User $b): ?Conversation
{
    $qb = $this->createQueryBuilder('c')
        ->join('c.participants', 'p')
        ->where('p = :a OR p = :b')
        ->groupBy('c.id')
        ->having('COUNT(p) = 2')
        ->setParameter('a', $a)
        ->setParameter('b', $b);

    return $qb->getQuery()->getOneOrNullResult();
}

}
