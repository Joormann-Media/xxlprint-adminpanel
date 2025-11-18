<?php

namespace App\Security;

use App\Entity\ReleaseFile;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class ReleaseFileVoter extends Voter
{
    public const DOWNLOAD = 'DOWNLOAD';

    public function __construct(
        private readonly Security $security
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::DOWNLOAD && $subject instanceof ReleaseFile;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var ReleaseFile $file */
        $file = $subject;

        // ✅ Public Files sind immer erlaubt
        if ($file->getIsPublic()) {
            return true;
        }

        // ✅ Nur bestimmte Rollen dürfen sonst downloaden
        return $this->security->isGranted('ROLE_CUSTOMER');
    }
}

