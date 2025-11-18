<?php

namespace App\Command;

use App\Entity\CityPostalcode;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'import:city-postalcodes')]
class CityPostalcodeImportCommand extends Command
{
    private const CSV_URL = 'https://downloads.suche-postleitzahl.org/v2/public/zuordnung_plz_ort.csv';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HttpClientInterface $httpClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Lädt die CSV-Datei automatisch herunter und importiert alle Städte/PLZ-Kombinationen.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("🌍 Lade CSV-Datei von <info>" . self::CSV_URL . "</info>...");

        try {
            $response = $this->httpClient->request('GET', self::CSV_URL);
            if (200 !== $response->getStatusCode()) {
                $output->writeln("<error>Fehler beim Herunterladen der Datei (Status: {$response->getStatusCode()})</error>");
                return Command::FAILURE;
            }

            $csvContent = $response->getContent();
            $tmpFile = sys_get_temp_dir() . '/plz_import_' . uniqid() . '.csv';
            file_put_contents($tmpFile, $csvContent);
        } catch (\Throwable $e) {
            $output->writeln("<error>Fehler beim Download: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        $output->writeln("📦 CSV gespeichert unter <comment>$tmpFile</comment>");
        $output->writeln("⏳ Starte Import...");

        try {
            $csv = Reader::createFromPath($tmpFile, 'r');
            $csv->setHeaderOffset(0);
            $records = Statement::create()->process($csv);

            $count = 0;
            foreach ($records as $record) {
                $entity = new CityPostalcode();
                $entity->setCity($record['ort']);
                $entity->setPostcode($record['plz']);
                $entity->setState($record['bundesland'] ?? null);

                $this->em->persist($entity);
                $count++;

                if ($count % 100 === 0) {
                    $this->em->flush();
                    $this->em->clear();
                    $output->writeln("✅ $count Datensätze importiert...");
                }
            }

            $this->em->flush();
            $output->writeln("🎉 Import abgeschlossen. Insgesamt: <info>$count</info> Einträge.");
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $output->writeln("<error>Fehler beim Verarbeiten der Datei: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}
