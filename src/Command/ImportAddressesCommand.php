<?php

namespace App\Command;

use App\Service\AddressImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

#[AsCommand(
    name: 'app:import-addresses',
    description: 'Importiert offizielle Adressen über die Overpass-API (inkl. Koordinaten/District-Fallback)',
)]
class ImportAddressesCommand extends Command
{
    public function __construct(
        private readonly AddressImportService $importService,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('start', null, InputOption::VALUE_REQUIRED, 'Start-PLZ', 46500)
            ->addOption('end', null, InputOption::VALUE_REQUIRED, 'End-PLZ', 46599)
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Maximale Anzahl Adressen', 500)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Nur anzeigen, nicht speichern');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $start = (int) $input->getOption('start');
        $end = (int) $input->getOption('end');
        $limit = (int) $input->getOption('limit');
        $dryRun = $input->getOption('dry-run');

        $io->title("📦 Importiere Adressen aus PLZ-Bereich $start bis $end");

        $all = [];
        $progress = new ProgressBar($output, ($end - $start + 1));
        $progress->start();

        for ($plz = $start; $plz <= $end; $plz++) {
            $batch = $this->importService->fetchFromOverpass($plz);

            if (!empty($batch)) {
                $all = array_merge($all, $batch);
                $output->writeln("\n📬 $plz → " . count($batch) . " Adressen gefunden");
            } else {
                $output->writeln("\n📭 $plz → Keine Treffer (Overpass & Fallback leer)");
            }

            $progress->advance();
            sleep(1); // Rate-Limit Overpass
        }

        $progress->finish();
        $output->writeln('');

        if (count($all) === 0) {
            $io->warning("❌ Keine Adressen gefunden im gesamten Bereich.");
            return Command::SUCCESS;
        }

        if ($limit > 0 && count($all) > $limit) {
            $io->note("⚠ Limitiert auf $limit Einträge (von insgesamt " . count($all) . ")");
            $all = array_slice($all, 0, $limit);
        }

        $io->success(count($all) . " Adressen insgesamt gesammelt.");

        // Vorschau der ersten 10
        $io->section("🔎 Vorschau der ersten " . min(10, count($all)) . " Adressen:");
        foreach (array_slice($all, 0, 10) as $addr) {
            $io->text(sprintf(
                '📍 %s, %s %s (%s) [%s, %s]',
                trim($addr->getStreet() . ' ' . $addr->getHouseNumber()),
                $addr->getPostcode(),
                $addr->getCity(),
                $addr->getDistrict() ?? 'kein District',
                $addr->getLat() ?? 'null',
                $addr->getLon() ?? 'null'
            ));
        }

        if ($dryRun) {
            $io->warning('🧪 DRY-RUN aktiv – es wurde nichts gespeichert.');
            return Command::SUCCESS;
        }

        $this->importService->persist($all, $this->em);
        $io->success("✅ Datenbank erfolgreich aktualisiert.");

        return Command::SUCCESS;
    }
}
