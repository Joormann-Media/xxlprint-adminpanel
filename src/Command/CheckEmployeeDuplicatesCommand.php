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
    name: 'employee:check-duplicates',
    description: 'Prüft doppelte Personalnummern und mögliche doppelte Mitarbeiter',
)]
class CheckEmployeeDuplicatesCommand extends Command
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

        $byNumber = [];
        $byName = [];
        $dupesNumber = [];
        $dupesName = [];

        foreach ($employees as $emp) {
            // === Prüfung 1: Personalnummer ===
            $nr = $emp->getEmployeeNumber();
            if ($nr) {
                $byNumber[$nr][] = $emp;
            }

            // === Prüfung 2: Name (+ optional Geburtsdatum) ===
            $key = strtolower(trim($emp->getFirstName() . ' ' . $emp->getLastName()));
            if ($emp->getBirthDate()) {
                $key .= '|' . $emp->getBirthDate()->format('Y-m-d');
            }
            $byName[$key][] = $emp;
        }

        // Ergebnisse sammeln
        foreach ($byNumber as $nr => $list) {
            if (count($list) > 1) {
                $dupesNumber[$nr] = $list;
            }
        }

        foreach ($byName as $key => $list) {
            if (count($list) > 1) {
                $dupesName[$key] = $list;
            }
        }

        // === Ausgabe ===
        if ($dupesNumber) {
            $io->section('⚠️ Doppelte Personalnummern');
            foreach ($dupesNumber as $nr => $list) {
                $io->writeln("Nummer $nr:");
                foreach ($list as $emp) {
                    $io->writeln(sprintf(
                        " - [%d] %s %s (Geburtsdatum: %s)",
                        $emp->getId(),
                        $emp->getFirstName(),
                        $emp->getLastName(),
                        $emp->getBirthDate()?->format('d.m.Y') ?? '-'
                    ));
                }
            }
        } else {
            $io->success('Keine doppelten Personalnummern gefunden ✅');
        }

        if ($dupesName) {
            $io->section('⚠️ Mögliche doppelte Mitarbeiter (Namens-Check)');
            foreach ($dupesName as $key => $list) {
                $io->writeln($key . ':');
                foreach ($list as $emp) {
                    $io->writeln(sprintf(
                        " - [%d] %s %s (Nr: %s, Geburtsdatum: %s)",
                        $emp->getId(),
                        $emp->getFirstName(),
                        $emp->getLastName(),
                        $emp->getEmployeeNumber(),
                        $emp->getBirthDate()?->format('d.m.Y') ?? '-'
                    ));
                }
            }
        } else {
            $io->success('Keine offensichtlichen Namens-Duplikate gefunden ✅');
        }

        return Command::SUCCESS;
    }
}
