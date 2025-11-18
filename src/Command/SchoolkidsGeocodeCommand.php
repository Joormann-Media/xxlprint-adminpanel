<?php

namespace App\Command;

use App\Entity\Schoolkids;
use App\Repository\SchoolkidsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:schoolkids:geocode',
    description: 'Füllt fehlende GPS-Koordinaten für Schoolkids über lokalen Nominatim-Server'
)]
class SchoolkidsGeocodeCommand extends Command
{
    public function __construct(
        private SchoolkidsRepository $kidsRepo,
        private EntityManagerInterface $em,
        private HttpClientInterface $httpClient,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $kids = $this->kidsRepo->findAll();
        $countUpdated = 0;

        foreach ($kids as $kid) {
            /** @var Schoolkids $kid */
            if ($kid->getLatitude() !== null && $kid->getLongitude() !== null) {
                continue; // schon Koordinaten vorhanden
            }

            $address = sprintf(
                '%s %s, %s %s',
                $kid->getStreet() ?? '',
                $kid->getStreetNumber() ?? '',
                $kid->getZip() ?? '',
                $kid->getCity() ?? ''
            );

            if (trim($address) === ',') {
                $io->warning("❌ Keine Adresse für Kid #{$kid->getId()}");
                continue;
            }

            $io->text("➡ Geocoding für Kid #{$kid->getId()} – $address");

            try {
                $response = $this->httpClient->request('GET', 'http://127.0.0.1:7071/search', [
                    'query' => [
                        'q' => $address,
                        'format' => 'json',
                        'addressdetails' => 1,
                        'limit' => 1,
                    ],
                ]);

                $data = $response->toArray();

                if (!empty($data)) {
                    $lat = (float) $data[0]['lat'];
                    $lon = (float) $data[0]['lon'];

                    $kid->setLatitude($lat);
                    $kid->setLongitude($lon);
                    $this->em->persist($kid);

                    $io->success("✅ Kid #{$kid->getId()} -> $lat,$lon");
                    $countUpdated++;
                } else {
                    $io->warning("⚠️ Keine Koordinaten gefunden für: $address");
                }
            } catch (\Throwable $e) {
                $io->error("Fehler bei Kid #{$kid->getId()}: " . $e->getMessage());
            }
        }

        $this->em->flush();

        $io->success("Fertig! $countUpdated Kids geokodiert.");

        return Command::SUCCESS;
    }
}
