<?php

namespace App\Sabre\Auth;

use Sabre\DAV\Auth\Backend\AbstractBasic;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class SymfonyUserBackend extends AbstractBasic
{
    private UserProviderInterface $userProvider;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserProviderInterface $userProvider, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userProvider = $userProvider;
        $this->passwordHasher = $passwordHasher;
    }

    protected function validateUserPass($username, $password)
    {
        try {
            $user = $this->userProvider->loadUserByIdentifier($username);

            if ($this->passwordHasher->isPasswordValid($user, $password)) {
                return true;
            }
        } catch (UserNotFoundException) {
            // Ignorieren
        }

        return false;
    }
}
