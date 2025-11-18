<?php

// src/Command/ImportGpsDataCommand.php
namespace App\Command;

use App\Entity\GpsPosition;
use App\Service\OrtungslogistikService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-gps-data')]
class ImportGpsDataCommand extends Command
{
    public function __construct(
        private OrtungslogistikService $olService,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->olService->fetchLiveData();

        foreach ($data as $entry) {
            $gps = new GpsPosition();
            $gps->setTimestampUtc(new \DateTimeImmutable()); // oder falls vorhanden: $entry['Timestamp']
            $gps->setLatitude($entry['Lat'] ?? 0);
            $gps->setLongitude($entry['Lng'] ?? 0);
            $gps->setSpeed($entry['Speed'] ?? null);
            $gps->setCourse($entry['Heading'] ?? null);
            $gps->setStatusText($entry['Description'] ?? null);
            $gps->setColor($entry['Color_LiveList'] ?? null);
            $gps->setClientId($entry['ClientID'] ?? 0);

            $this->em->persist($gps);
        }

        $this->em->flush();
        $output->writeln(count($data) . ' GPS-Datensätze importiert.');

        return Command::SUCCESS;
    }
}

