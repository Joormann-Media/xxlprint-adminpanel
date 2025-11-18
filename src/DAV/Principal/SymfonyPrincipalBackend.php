<?php

namespace App\DAV\Principal;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sabre\DAVACL\PrincipalBackend\BackendInterface;
use Sabre\DAV\Exception\Forbidden;

class SymfonyPrincipalBackend implements BackendInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function getPrincipalsByPrefix($prefixPath)
    {
        $users = $this->em->getRepository(User::class)->findAll();

        $principals = [];
        foreach ($users as $user) {
            $principals[] = [
                'uri' => $prefixPath . '/' . $user->getEmail(),
                '{DAV:}displayname' => $user->getUsername() ?? $user->getEmail(),
            ];
        }

        return $principals;
    }

    public function getPrincipalByPath($path)
    {
        $email = basename($path);
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) return null;

        return [
            'uri' => $path,
            '{DAV:}displayname' => $user->getUsername() ?? $user->getEmail(),
        ];
    }

    public function updatePrincipal($path, $mutations)
    {
        throw new Forbidden('Read-only');
    }

    public function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof')
    {
        return [];
    }

    public function getGroupMemberSet($principal)
    {
        return [];
    }

    public function getGroupMembership($principal)
    {
        return [];
    }

    public function setGroupMemberSet($principal, array $members)
    {
        throw new Forbidden('Groups are not supported');
    }

    public function findByUri($uri, $principalPrefix)
    {
        return $this->getPrincipalByPath($principalPrefix . '/' . basename($uri));
    }
}
