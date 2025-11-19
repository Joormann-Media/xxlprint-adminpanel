<?php

namespace App\Command;

use App\Entity\UserRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fix-user-roles',
    description: 'Korrigiert role_tag und Hierarchien in der user_roles Tabelle.'
)]
class FixUserRolesCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(UserRoles::class);

        $output->writeln("<info>📌 Starte Role Migration…</info>");

        $hierarchyMap = [
            'ROLE_SUPREME_ADMIN'  => 9999,
            'ROLE_SYSADMIN'       => 9000,
            'ROLE_ADMIN'          => 8000,
            'ROLE_USERADMIN'      => 7000,
            'ROLE_RELEASEMANAGER' => 6000,
            'ROLE_WEBSITEADMIN'   => 5000,
            'ROLE_MODERATOR'      => 4000,
            'ROLE_CUSTOMERADMIN'  => 3000,
            'ROLE_CUSTOMER'       => 2000,
            'ROLE_DEVELOPER'      => 1500,
            'ROLE_USER'           => 1000,
        ];

        $roles = $repo->findAll();

        foreach ($roles as $role) {
            $name = $role->getRoleName();

            if (!isset($hierarchyMap[$name])) {
                $output->writeln("<comment>⚠ Unbekannte Rolle übersprungen: $name</comment>");
                continue;
            }

            $role->setRoleTag($name);
            $role->setHierarchy($hierarchyMap[$name]);

            $output->writeln("<info>✔ $name korrigiert</info>");
        }

        $this->em->flush();

        $output->writeln("<info>🎉 Fertig! Rollen erfolgreich korrigiert.</info>");

        return Command::SUCCESS;
    }
}
