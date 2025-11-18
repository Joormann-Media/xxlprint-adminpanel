<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserToken;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class TokenService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserTokenRepository $tokenRepo
    ) {}

    public function generateToken(User $user, string $type, int $validMinutes = 60): UserToken
    {
        $token = (new UserToken())
            ->setUser($user)
            ->setType($type)
            ->setToken(Uuid::v4()->toRfc4122())
            ->setCreatedAt(new \DateTime())
            ->setExpiresAt((new \DateTime())->modify("+{$validMinutes} minutes"))
            ->setUsed(false);

        $this->em->persist($token);
        $this->em->flush();

        return $token;
    }

    public function validateToken(string $token, string $type): ?UserToken
    {
        $userToken = $this->tokenRepo->findOneBy([
            'token' => $token,
            'type' => $type,
            'used' => false,
        ]);

        if (!$userToken || $userToken->getExpiresAt() < new \DateTime()) {
            return null;
        }

        return $userToken;
    }

    public function markAsUsed(UserToken $userToken): void
    {
        $userToken->setUsed(true);
        $this->em->flush();
    }
}
