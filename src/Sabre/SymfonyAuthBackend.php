<?php

namespace App\Sabre;

use Doctrine\ORM\EntityManagerInterface;
use Sabre\DAV\Auth\Backend\AbstractBasic;

class SymfonyAuthBackend extends AbstractBasic
{
    public function __construct(private EntityManagerInterface $em) {}

    protected function validateUserPass($username, $password): bool
    {
        $user = $this->em->getRepository(\App\Entity\User::class)->findOneBy(['email' => $username]);

        if (!$user) {
            return false;
        }

        return password_verify($password, $user->getPassword());
    }
}
