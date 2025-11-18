<?php
namespace App\Command;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:employee:remove-duplicates',
    description: 'Entfernt doppelte Employees (gleicher Vor- und Nachname)',
)]
class RemoveEmployeeDuplicatesCommand extends Command
{
    private EmployeeRepository $employeeRepo;
    private EntityManagerInterface $em;

    public function __construct(EmployeeRepository $employeeRepo, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->employeeRepo = $employeeRepo;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Starte Doppelsuche ...</info>');

        // 1. Alle Employees holen und nach Name gruppieren
        $allEmployees = $this->employeeRepo->findAll();
        $map = [];

        foreach ($allEmployees as $employee) {
            $key = mb_strtolower(trim($employee->getFirstName() . '|' . $employee->getLastName()));
            if (!isset($map[$key])) {
                $map[$key] = [];
            }
            $map[$key][] = $employee;
        }

        $countTotal = 0;
        $countRemoved = 0;

        foreach ($map as $group) {
            if (count($group) > 1) {
                // Sortieren nach ID, damit der erste (älteste) bleibt
                usort($group, fn($a, $b) => $a->getId() <=> $b->getId());

                // Alle außer den ersten löschen
                $keep = array_shift($group);
                $output->writeln(sprintf(
                    '<comment>%s %s (ID %d) bleibt, %d Duplikate werden gelöscht</comment>',
                    $keep->getFirstName(),
                    $keep->getLastName(),
                    $keep->getId(),
                    count($group)
                ));
                foreach ($group as $dup) {
                    $this->em->remove($dup);
                    $output->writeln(sprintf(' - Entferne ID %d', $dup->getId()));
                    $countRemoved++;
                }
                $countTotal++;
            }
        }

        $this->em->flush();

        $output->writeln("<info>Entfernte Doubletten: $countRemoved in $countTotal Gruppen</info>");
        $output->writeln('<info>Fertig!</info>');

        return Command::SUCCESS;
    }
}
