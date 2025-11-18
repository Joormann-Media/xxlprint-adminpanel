<?php

// src/Command/SanitizeVehiclesCommand.php

namespace App\Command;

use App\Entity\Vehicle;
use App\Service\SanitizerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:sanitize:vehicles',
    description: 'Sanitizes all vehicles and updates sanitized fields.',
)]
class SanitizeVehiclesCommand extends Command
{
    private EntityManagerInterface $em;
    private SanitizerService $sanitizer;

    public function __construct(EntityManagerInterface $em, SanitizerService $sanitizer)
    {
        parent::__construct();
        $this->em = $em;
        $this->sanitizer = $sanitizer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(Vehicle::class);
        $vehicles = $repo->findAll();

        if (empty($vehicles)) {
            $output->writeln('<comment>Keine Fahrzeuge gefunden.</comment>');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($vehicles as $vehicle) {
            if (method_exists($vehicle, 'updateSanitizedFields')) {
                $vehicle->updateSanitizedFields($this->sanitizer);
                $count++;
            }
        }

        $this->em->flush();

        $output->writeln("<info>$count Fahrzeuge wurden aktualisiert und sanitisiert.</info>");

        return Command::SUCCESS;
    }
}

