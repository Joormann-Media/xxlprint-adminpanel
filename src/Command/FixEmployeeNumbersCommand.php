<?php

namespace App\Command;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'employee:fix-numbers',
    description: 'Normalisiert alle employeeNumber auf 4-stellige Werte (mit führenden Nullen)',
)]
class FixEmployeeNumbersCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repo = $this->em->getRepository(Employee::class);

        $employees = $repo->findAll();
        $updated = 0;

        foreach ($employees as $employee) {
            $number = $employee->getEmployeeNumber();

            if ($number === null) {
                continue; // keine Nummer vorhanden
            }

            // Normalisieren: nur Zahlen + führende Nullen auf 4 Stellen
            $normalized = str_pad((string) intval($number), 4, '0', STR_PAD_LEFT);

            if ($normalized !== $number) {
                $employee->setEmployeeNumber($normalized);
                $updated++;
                $io->text(sprintf(
                    'Fix: %s %s → %s',
                    $employee->getFirstName(),
                    $employee->getLastName(),
                    $normalized
                ));
            }
        }

        if ($updated > 0) {
            $this->em->flush();
        }

        $io->success(sprintf('Fertig. %d Mitarbeiter angepasst.', $updated));

        return Command::SUCCESS;
    }
}
