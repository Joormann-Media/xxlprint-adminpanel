<?php

namespace App\Command;

use App\Service\AddressImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:import-addresses-stapel',
    description: 'Importiert Adressen für einen Stapel Postleitzahlen aus OSM/Overpass (Batch, transaktionssicher, mit Stapelverarbeitung)',
)]
class StapleImportAddressesCommand extends Command
{
    public function __construct(
        private readonly AddressImportService $addressImportService,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'plz',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Liste der Postleitzahlen (z.B. 46483 46485 46487 ...)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var array<string> $plzList */
        $plzList = $input->getArgument('plz');
        $io->title('Stapel-Adressimport per Overpass/OSM für folgende PLZ:');
        $io->listing($plzList);

        $result = $this->addressImportService->getAddressesForPlzList($plzList);
        $addresses = $result['addresses'];
        $skippedPlz = $result['skippedPlz'];

        if (!empty($skippedPlz)) {
            $io->note('Folgende PLZ sind bereits in der Datenbank und werden übersprungen:');
            $io->listing($skippedPlz);
        }

        if (empty($addresses)) {
            $io->success('Keine neuen PLZ zu importieren – alles schon in der Datenbank.');
            return Command::SUCCESS;
        }

        $io->progressStart(count($addresses));

        $importResult = $this->addressImportService->persist($addresses, $this->em);

        $io->progressFinish();

        if ($importResult['success'] ?? false) {
            $io->success(sprintf(
                "%d Adressen eingefügt, %d übersprungen.",
                $importResult['inserted'],
                $importResult['skipped']
            ));
        } else {
            $io->error('Fehler während des Imports. Datenbank wurde zurückgesetzt.');
        }

        if (!empty($importResult['errors'])) {
            $io->warning(sprintf('%d Fehler beim Import:', count($importResult['errors'])));
            foreach (array_slice($importResult['errors'], 0, 10) as $error) {
                $io->writeln(sprintf(
                    '• [%s] %s %s – %s',
                    $error['plz'] ?? '-',
                    $error['street'] ?? '-',
                    $error['houseNumber'] ?? '-',
                    $error['reason'] ?? '-'
                ));
            }
            if (count($importResult['errors']) > 10) {
                $io->note('Weitere Fehler im Log.');
            }
        }

        return Command::SUCCESS;
    }
}
