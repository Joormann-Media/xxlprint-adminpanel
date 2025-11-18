<?php

namespace App\Command;

use App\Repository\OfficialAddressRepository;
use App\Service\ReverseGeocodeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:geo:district-patch',
    description: 'Ergänzt fehlende districts per Reverse-Geocoding (patcht nur, wenn valid=0, optional: --all = auch vorhandene Districts überschreiben, --synch = valid auf true setzen, wenn locationComment befüllt ist)',
)]
class DistrictPatchCommand extends Command
{
    public function __construct(
        private readonly OfficialAddressRepository $repo,
        private readonly ReverseGeocodeService $geocoder,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('postcode', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Optional: Nur Adressen mit dieser/mehreren Postleitzahlen patchen (mehrere PLZ durch Leerzeichen getrennt)')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Auch Adressen mit bereits gesetztem District neu patchen')
            ->addOption('auto-confirm', null, InputOption::VALUE_NONE, 'Automatisch übernehmen, ohne Rückfrage')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Maximal zu bearbeitende Datensätze', null)
            ->addOption('synch', null, InputOption::VALUE_NONE, 'Existierende Adressen mit locationComment auf valid=true setzen (ohne Überschreiben)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $postcodes = $input->getArgument('postcode');
        $patchAll = $input->getOption('all');
        $autoConfirm = $input->getOption('auto-confirm');
        $synchMode = $input->getOption('synch');
        $limit = $input->getOption('limit') ? (int) $input->getOption('limit') : null;
        $flushLimit = 20;
        $batchSize = 10;

        $qb = $this->repo->createQueryBuilder('a')
            ->where('a.lat IS NOT NULL')
            ->andWhere('a.lon IS NOT NULL')
            ->andWhere('a.valid = 0');

        // PLZ-Filter
        if (!empty($postcodes)) {
            if (count($postcodes) === 1) {
                $qb->andWhere('a.postcode = :plz')
                   ->setParameter('plz', $postcodes[0]);
            } else {
                $qb->andWhere('a.postcode IN (:plz)')
                   ->setParameter('plz', $postcodes);
            }
        }

        // Nur District null, außer bei --all
        if (!$patchAll) {
            $qb->andWhere('a.district IS NULL');
        }

        $iterable = $qb->orderBy('a.id', 'ASC')->getQuery()->toIterable();

        // Count Query (optional)
        $total = null;
        if (method_exists($qb, 'getQuery')) {
            $qbCount = clone $qb;
            $qbCount->select('COUNT(a.id)');
            $total = (int) $qbCount->getQuery()->getSingleScalarResult();
        }

        if ($total === 0) {
            $msg = !empty($postcodes)
                ? "🎉 Keine passenden Adressen in PLZ(s) " . implode(', ', $postcodes) . " gefunden."
                : "🎉 Keine passenden Adressen gefunden.";
            $io->success($msg);
            return Command::SUCCESS;
        }

        $modeText = $synchMode
            ? "🔄 SYNC-Modus: valid auf true setzen, wenn location_comment befüllt ist"
            : ($patchAll
                ? "⚠️  Patch-Modus: ALLE Adressen (district wird überschrieben, aber nur wo valid=0!)"
                : "🏙️  Nur Adressen ohne District (wo valid=0)");
        $modeText .= !empty($postcodes) ? " in PLZ(s): " . implode(', ', $postcodes) : '';

        $io->title(($total !== null ? "($total Treffer) " : "") . $modeText);
        $patched = 0;

        $batch = [];
        $block = 1;
        $missingGeoFields = [];
        $processed = 0;
        $syncDone = false;

        foreach ($iterable as $address) {
            if ($limit && $processed >= $limit) {
                $io->warning("Limit von $limit Datensätzen erreicht. Abbruch.");
                break;
            }
            $batch[] = $address;
            $processed++;
            if (count($batch) >= $batchSize) {
                $io->section("🔢 Block #$block");
                $patched += $this->processAddressBatch($batch, $io, $autoConfirm, $synchMode, $flushLimit, $patched, $missingGeoFields, $limit, $processed, $syncDone);
                $batch = [];
                $block++;
                $this->em->clear();
                if ($syncDone) {
                    break;
                }
            }
        }
        // Restliche Adressen abarbeiten
        if ($batch && !$syncDone) {
            $io->section("🔢 Block #$block");
            $patched += $this->processAddressBatch($batch, $io, $autoConfirm, $synchMode, $flushLimit, $patched, $missingGeoFields, $limit, $processed, $syncDone);
            $this->em->clear();
        }

        $this->em->flush();
        $io->success("🎯 Abgeschlossen: $patched Adressen verarbeitet.");

        // Am Ende: Ausgabe & Export der Adressen mit komplett leeren location_comment
        if ($synchMode && count($missingGeoFields)) {
            $io->warning('Adressen OHNE location_comment:');
            foreach ($missingGeoFields as $address) {
                $io->writeln(" - [{$address->getId()}] {$address->getStreet()} {$address->getHouseNumber()}, {$address->getPostcode()} {$address->getCity()}");
            }
            // CSV-Export
            $filename = 'missing_locationcomment_' . date('Ymd_His') . '.csv';
            $csv = fopen($filename, 'w');
            fputcsv($csv, ['ID','PLZ','Stadt','Straße','Hausnummer','LocationComment']);
            foreach ($missingGeoFields as $address) {
                fputcsv($csv, [
                    $address->getId(),
                    $address->getPostcode(),
                    $address->getCity(),
                    $address->getStreet(),
                    $address->getHouseNumber(),
                    $address->getLocationComment(),
                ]);
            }
            fclose($csv);
            $io->success("Exportiert als $filename (" . count($missingGeoFields) . " Zeilen)");
        }

        return Command::SUCCESS;
    }

    /**
     * Verarbeitet einen Batch von Adressen. Gibt Anzahl gepatchter/gesynchdter zurück.
     */
    private function processAddressBatch(
        array $batch,
        SymfonyStyle $io,
        bool $autoConfirm,
        bool $synchMode,
        int $flushLimit,
        int $patchedSoFar,
        array &$missingGeoFields,
        ?int $limit = null,
        int $processed = 0,
        bool &$syncDone = false
    ): int
    {
        $patched = 0;
        foreach ($batch as $address) {
            $lat = $address->getLat();
            $lon = $address->getLon();

            if ($lat === null || $lon === null) {
                $io->warning("⚠️  Keine Koordinaten – übersprungen.");
                continue;
            }

            // --- SYNC-MODUS ---
            if ($synchMode) {
                $locComment = $address->getLocationComment();
                $io->note("SYNC DEBUG: ID={$address->getId()} LocationComment=" . var_export($locComment, true) . " Valid=" . var_export($address->isValid(), true));
                if (trim((string)$locComment) !== '') {
                    if (!$address->isValid()) {
                        $address->setValid(true);
                        $address->setUpdatedAt(new \DateTime());
                        $this->em->persist($address);
                        $io->success("✅ VALID gesetzt: {$address->getStreet()} {$address->getHouseNumber()}, {$address->getPostcode()} {$address->getCity()}");
                        $io->note("DEBUG: valid=" . var_export($address->isValid(), true) . " ID={$address->getId()}");
                        $patched++;
                    } else {
                        $io->note("Schon valid: {$address->getStreet()} {$address->getHouseNumber()}");
                    }
                } else {
                    $io->warning("❌ Kein location_comment, valid bleibt false: [{$address->getId()}] {$address->getStreet()} {$address->getHouseNumber()}");
                    $missingGeoFields[] = $address;
                }
                if ($limit && $patchedSoFar + $patched >= $limit) {
                    $io->warning("SYNC: Limit von $limit erreicht. Batch wird hier abgebrochen.");
                    $syncDone = true;
                    break;
                }
                continue; // Synch: niemals etwas überschreiben!
            }

            // --- PATCH-MODUS (Standard/--all) ---
            $geoData = $this->geocoder->getFullAddressData($lat, $lon);
            if (!$geoData['district'] ?? null) {
                $io->warning("🚫 Kein District ermittelbar – übersprungen.");
                continue;
            }

            $io->writeln("➡️  {$address->getStreet()} {$address->getHouseNumber()}, {$address->getPostcode()} {$address->getCity()}");
            $io->writeln("🌐 Koordinaten: $lat, $lon");
            $io->writeln("💡 Vorschlag: " . ($geoData['district'] ?? ''));

            if ($autoConfirm || $io->confirm("✔️  Übernehmen?", false)) {
                $address->setDistrict($geoData['district']);
                $address->setNeighbourhood($geoData['neighbourhood'] ?? null);
                $address->setSubdistrict($geoData['subdistrict'] ?? null);
                $address->setLocationComment($geoData['locationComment'] ?? null);
                $address->setUpdatedAt(new \DateTime());
                $address->setValid(true);
                $this->em->persist($address);
                $io->note("DEBUG: valid=" . var_export($address->isValid(), true) . " ID={$address->getId()}");

                $patched++;

                if ($limit && $patchedSoFar + $patched >= $limit) {
                    $io->warning("PATCH: Limit von $limit erreicht. Batch wird hier abgebrochen.");
                    $syncDone = true;
                    break;
                }

                if ((($patchedSoFar + $patched) % $flushLimit) === 0) {
                    $this->em->flush();
                    $this->em->clear();
                    $io->info("💾 Zwischengespeichert (" . ($patchedSoFar + $patched) . ")");
                }

                $io->success("✅ Gespeichert.");
            } else {
                $io->note("⏭️  Übersprungen.");
            }
        }
        return $patched;
    }
}
