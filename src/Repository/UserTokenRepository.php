<?php

namespace App\Repository;

use App\Entity\UserToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<UserToken>
 *
 * @method UserToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserToken[]    findAll()
 * @method UserToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserToken::class);
    }
    public function findValidMobileToken(User $user): ?UserToken
{
    return $this->createQueryBuilder('t')
        ->andWhere('t.user = :user')
        ->andWhere('t.type = :type')
        ->andWhere('t.expiresAt > :now')
        ->andWhere('t.used = false')
        ->setParameter('user', $user)
        ->setParameter('type', 'mobile_verification')
        ->setParameter('now', new \DateTimeImmutable())
        ->orderBy('t.createdAt', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
}

public function invalidateOldTokens(User $user, string $type): void
{
    $qb = $this->createQueryBuilder('t')
        ->update()
        ->set('t.used', ':used')
        ->where('t.user = :user')
        ->andWhere('t.type = :type')
        ->andWhere('t.used = false')
        ->setParameter('used', true)
        ->setParameter('user', $user)
        ->setParameter('type', $type);

    $qb->getQuery()->execute();
}

}
