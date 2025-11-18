<?php

namespace App\Command;

use App\Repository\OfficialAddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:scan-nonvalid-official-address',
    description: 'Listet alle Postleitzahlen mit mindestens einem nicht validen Datensatz aus official_address.',
)]
class ScanNonvalidOfficialAddressCommand extends Command
{
    public function __construct(
        private readonly OfficialAddressRepository $repo,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('PLZ-Scanner für nicht-validierte Adressen');

        // Rohdaten sammeln: alle PLZ mit valid = 0
        $qb = $this->repo->createQueryBuilder('a')
            ->select('DISTINCT a.postcode')
            ->where('a.valid = 0')
            ->andWhere('a.postcode IS NOT NULL')
            ->orderBy('a.postcode', 'ASC');

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            $io->success('Alle Postleitzahlen haben gültige Adressen. Keine Leichen im Keller! 🎉');
            return Command::SUCCESS;
        }

        // Flache Liste erzeugen
        $plzList = array_map(fn($row) => $row['postcode'], $results);

        $io->success("Folgende PLZ haben mindestens einen nicht-validen Eintrag (valid = 0):");
        $io->listing($plzList);

        $io->note('Gesamt: ' . count($plzList) . ' verschiedene PLZ gefunden.');

        return Command::SUCCESS;
    }
}
