<?php

namespace App\Repository;

use App\Entity\MessageStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class MessageStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageStatus::class);
    }

    /**
     * Zählt alle ungelesenen Nachrichten für den gegebenen User.
     */
    public function countUnreadForUser($user): int
    {
        // UserGroups holen (Array oder Collection)
        $userGroups = $user->getUsergroups();
        if ($userGroups instanceof \Doctrine\Common\Collections\Collection) {
            $userGroups = $userGroups->toArray();
        }
        $groupIds = array_map(
            fn($g) => is_object($g) ? $g->getId() : $g,
            $userGroups
        );

        // QueryBuilder zuerst initialisieren!
        $qb = $this->createQueryBuilder('ms')
            ->innerJoin('ms.recipient', 'mr')
            ->andWhere('ms.displayedAt IS NULL');

        // Empfängerbedingungen
        $orX = $qb->expr()->orX(
            'mr.recipientUser = :user',
            !empty($groupIds) ? 'mr.recipientGroup IN (:groupIds)' : '1=0',
            'mr.isAll = true'
        );
        $qb->andWhere($orX)
           ->setParameter('user', $user);

        if (!empty($groupIds)) {
            $qb->setParameter('groupIds', $groupIds);
        }

        return (int) $qb->select('COUNT(ms.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Zählt alle ungelesenen und "dringenden" Nachrichten für den User.
     */
    public function countUrgentNewsForUser($user): int
    {
        $userGroups = $user->getUsergroups();
        if ($userGroups instanceof \Doctrine\Common\Collections\Collection) {
            $userGroups = $userGroups->toArray();
        }
        $groupIds = array_map(
            fn($g) => is_object($g) ? $g->getId() : $g,
            $userGroups
        );

        $qb = $this->createQueryBuilder('ms')
            ->innerJoin('ms.recipient', 'mr')
            ->innerJoin('mr.message', 'm')
            ->andWhere('ms.displayedAt IS NULL')
            ->andWhere('m.isUrgent = 1');

        $orX = $qb->expr()->orX(
            'mr.recipientUser = :user',
            !empty($groupIds) ? 'mr.recipientGroup IN (:groupIds)' : '1=0',
            'mr.isAll = true'
        );
        $qb->andWhere($orX)
           ->setParameter('user', $user);

        if (!empty($groupIds)) {
            $qb->setParameter('groupIds', $groupIds);
        }

        return (int) $qb->select('COUNT(ms.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findUnreadMessagesForUser($user)
{
    $userGroups = $user->getUsergroups();
    if ($userGroups instanceof \Doctrine\Common\Collections\Collection) {
        $userGroups = $userGroups->toArray();
    }
    $groupIds = array_map(
        fn($g) => is_object($g) ? $g->getId() : $g,
        $userGroups
    );

    $qb = $this->createQueryBuilder('ms')
        ->innerJoin('ms.recipient', 'mr')
        ->innerJoin('mr.message', 'm')
        ->andWhere('ms.displayedAt IS NULL');

    $orX = $qb->expr()->orX(
        'mr.recipientUser = :user',
        !empty($groupIds) ? 'mr.recipientGroup IN (:groupIds)' : '1=0',
        'mr.isAll = true'
    );
    $qb->andWhere($orX)
        ->setParameter('user', $user);

    if (!empty($groupIds)) {
        $qb->setParameter('groupIds', $groupIds);
    }

    // z. B. sortiert nach Dringlichkeit und Datum
    $qb->orderBy('m.isUrgent', 'DESC')
       ->addOrderBy('m.createdAt', 'DESC');

    // Gibt die MessageStatus-Objekte zurück, jeweils mit Empfänger + Message
    return $qb->getQuery()->getResult();
}
public function findAllMessagesForUser($user): array
{
    $userGroups = $user->getUsergroups();
    if ($userGroups instanceof \Doctrine\Common\Collections\Collection) {
        $userGroups = $userGroups->toArray();
    }
    $groupIds = array_map(
        fn($g) => is_object($g) ? $g->getId() : $g,
        $userGroups
    );

    $qb = $this->createQueryBuilder('ms')
        ->leftJoin('ms.recipient', 'mr')
        ->leftJoin('mr.message', 'm')
        ->addSelect('mr', 'm');

    $orX = $qb->expr()->orX(
        'mr.recipientUser = :user',
        !empty($groupIds) ? 'mr.recipientGroup IN (:groupIds)' : '1=0',
        'mr.isAll = true'
    );

    $qb->andWhere($orX)
       ->setParameter('user', $user);

    if (!empty($groupIds)) {
        $qb->setParameter('groupIds', $groupIds);
    }

    return $qb
        ->orderBy('m.isUrgent', 'DESC')
        ->addOrderBy('m.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
}
/**
 * Gibt für jeden Absender nur die letzte Nachricht zurück (wie bei WhatsApp)
 */
public function findLastMessagesPerSenderForUser(\App\Entity\User $user): array
{
    $all = $this->findAllMessagesForUser($user);

    $latestBySender = [];
    foreach ($all as $status) {
        $senderId = $status->getRecipient()->getMessage()->getSender()->getId();
        if (!isset($latestBySender[$senderId])) {
            $latestBySender[$senderId] = $status;
        }
    }

    return $latestBySender;
}
public function findConversationBetweenUserAndSender(User $user, int $senderId): array
{
    $userGroups = $user->getUsergroups();
    if ($userGroups instanceof \Doctrine\Common\Collections\Collection) {
        $userGroups = $userGroups->toArray();
    }
    $groupIds = array_map(
        fn($g) => is_object($g) ? $g->getId() : $g,
        $userGroups
    );

    $qb = $this->createQueryBuilder('ms')
        ->innerJoin('ms.recipient', 'mr')
        ->innerJoin('mr.message', 'm')
        ->where('m.sender = :senderId')
        ->andWhere(
            $qb->expr()->orX(
                'mr.recipientUser = :user',
                !empty($groupIds) ? 'mr.recipientGroup IN (:groupIds)' : '1=0',
                'mr.isAll = true'
            )
        )
        ->setParameter('senderId', $senderId)
        ->setParameter('user', $user)
        ->orderBy('m.createdAt', 'ASC');

    if (!empty($groupIds)) {
        $qb->setParameter('groupIds', $groupIds);
    }

    return $qb->getQuery()->getResult();
}

public function countUnreadMessagesForUser(User $user): int
{
    return $this->createQueryBuilder('ms')
        ->select('COUNT(ms.id)')
        ->join('ms.recipient', 'mr')
        ->join('mr.message', 'm')
        ->where('mr.recipientUser = :user')
        ->andWhere('ms.displayedAt IS NULL')
        ->setParameter('user', $user)
        ->getQuery()
        ->getSingleScalarResult();
}
public function countUrgentUnreadMessagesForUser(User $user): int
{
    return $this->createQueryBuilder('ms')
        ->select('COUNT(ms.id)')
        ->join('ms.recipient', 'mr')
        ->join('mr.message', 'm')
        ->where('mr.recipientUser = :user')
        ->andWhere('ms.displayedAt IS NULL')
        ->andWhere('m.isUrgent = true') // ← falls du so ein Feld hast
        ->setParameter('user', $user)
        ->getQuery()
        ->getSingleScalarResult();
}

}
