<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\UserToken;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;

class MobileVerificationTokenManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserTokenRepository $tokenRepo
    ) {}

    public function createToken(User $user): string
    {
        $code = (string) random_int(100000, 999999);
        $now = new \DateTimeImmutable();
        $expires = $now->modify('+10 minutes');

        // Optional: alte ungenutzte Tokens invalidieren
        $this->tokenRepo->invalidateOldTokens($user, 'mobile_verification');

        $token = new UserToken();
        $token->setUser($user);
        $token->setToken($code);
        $token->setType('mobile_verification');
        $token->setCreatedAt($now);
        $token->setExpiresAt($expires);
        $token->setUsed(false);

        $this->em->persist($token);
        $this->em->flush();

        return $code;
    }

    public function validateToken(User $user, string $inputCode): bool
    {
        $token = $this->tokenRepo->findValidMobileToken($user);

        if (!$token || $token->isUsed()) {
            return false;
        }

        if ($token->getExpiresAt() < new \DateTimeImmutable()) {
            return false;
        }

        if ($token->getToken() !== $inputCode) {
            return false;
        }

        // Mark as used
        $token->setUsed(true);
        $this->em->flush();

        return true;
    }
}
