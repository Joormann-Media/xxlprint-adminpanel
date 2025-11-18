<?php

namespace App\Command;

use App\Entity\School;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'school:fix-geocoords',
    description: 'Trägt für alle Schools die Lat/Lon von der verknüpften Adresse nach',
)]
class SchoolGeocoordUpdateCommand extends Command
{
    public function __construct(
        private readonly SchoolRepository $schoolRepo,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Keine Änderungen speichern (nur anzeigen)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dryRun = $input->getOption('dry-run');

        $schools = $this->schoolRepo->findAll();

        $count = 0;
        $changed = 0;

        foreach ($schools as $school) {
            if (!$school->getAddress()) {
                continue;
            }
            $addr = $school->getAddress();

            // Immer wenn lat/lon fehlen oder ungleich Adresse (Dubletten vermeiden)
            $lat = $addr->getLat();
            $lon = $addr->getLon();
            if ($lat && $lon) {
                if ($school->getLatitude() !== $lat || $school->getLongitude() !== $lon) {
                    $output->writeln(sprintf(
                        "Schule <info>#%d %s</info>: Setze Koordinaten von Adresse #%d (%f, %f)",
                        $school->getId(),
                        $school->getName(),
                        $addr->getId(),
                        $lat, $lon
                    ));
                    if (!$dryRun) {
                        $school->setLatitude($lat);
                        $school->setLongitude($lon);
                    }
                    $changed++;
                }
                $count++;
            }
        }

        if (!$dryRun) {
            $this->em->flush();
            $output->writeln("<info>Alle Änderungen gespeichert.</info>");
        } else {
            $output->writeln("<comment>DRY-RUN: Keine Änderungen gespeichert.</comment>");
        }

        $output->writeln(sprintf(
            "<info>%d Schulen mit Adresse gefunden. %d Koordinaten aktualisiert.</info>",
            $count, $changed
        ));
        return Command::SUCCESS;
    }
}
