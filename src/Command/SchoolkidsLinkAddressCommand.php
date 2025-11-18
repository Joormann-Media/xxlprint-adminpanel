<?php

namespace App\Command;

use App\Entity\Schoolkids;
use App\Entity\OfficialAddress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:schoolkids:link-address',
    description: 'Verknüpft Schoolkids mit passender OfficialAddress anhand der Adressfelder (street, streetNumber, zip, city)'
)]
class SchoolkidsLinkAddressCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $kidRepo = $this->em->getRepository(Schoolkids::class);
        $addressRepo = $this->em->getRepository(OfficialAddress::class);

        $kids = $kidRepo->findAll();
        $linked = 0;
        $notLinked = [];

        foreach ($kids as $kid) {
            $street = $kid->getStreet();
            $houseNumber = $kid->getStreetNumber();
            $zip = $kid->getZip();
            $city = $kid->getCity();
            $name = trim(($kid->getFirstName() ?? '') . ' ' . ($kid->getLastName() ?? ''));

            // Bereinige/Normalisiere PLZ (z. B. 46562.0 → 46562)
            $zipNorm = $this->normalizePostcode($zip);

            // Baue normalisierte Adresse wie in OfficialAddress
            $normalized = OfficialAddress::buildNormalized($street, $houseNumber, $zipNorm, $city);

            if (!$street || !$houseNumber || !$zipNorm || !$city) {
                $msg = sprintf(
                    '[%d] %s: %s %s %s %s <error>(unvollständig)</error>',
                    $kid->getId(), $name, $street, $houseNumber, $zipNorm, $city
                );
                $output->writeln($msg);
                $notLinked[] = [
                    'id' => $kid->getId(),
                    'name' => $name,
                    'street' => $street,
                    'houseNumber' => $houseNumber,
                    'zip' => $zip,
                    'city' => $city,
                    'grund' => 'unvollständig'
                ];
                continue;
            }

            $found = $addressRepo->findOneBy(['normalized' => $normalized]);

            if ($found) {
                $kid->setAddress($found);
                $this->em->persist($kid);
                $output->writeln(sprintf(
                    '[%d] %s: %s %s, %s %s <info>→ #%d verknüpft</info>',
                    $kid->getId(), $name, $street, $houseNumber, $zipNorm, $city, $found->getId()
                ));
                $linked++;
            } else {
                $msg = sprintf(
                    '[%d] %s: %s %s, %s %s <error>→ NICHT gefunden!</error>',
                    $kid->getId(), $name, $street, $houseNumber, $zipNorm, $city
                );
                $output->writeln($msg);
                $notLinked[] = [
                    'id' => $kid->getId(),
                    'name' => $name,
                    'street' => $street,
                    'houseNumber' => $houseNumber,
                    'zip' => $zipNorm,
                    'city' => $city,
                    'grund' => 'nicht gefunden'
                ];
            }
        }

        $this->em->flush();

        $output->writeln('');
        $output->writeln("<info>Fertig! $linked Schoolkids verknüpft.</info>");
        if ($notLinked) {
            $output->writeln("<error>Keine passende Adresse gefunden für folgende Kinder:</error>");
            foreach ($notLinked as $fail) {
                $output->writeln(sprintf(
                    '[%d] %s: %s %s, %s %s (%s)',
                    $fail['id'],
                    $fail['name'],
                    $fail['street'] ?? '-',
                    $fail['houseNumber'] ?? '',
                    $fail['zip'] ?? '-',
                    $fail['city'] ?? '-',
                    $fail['grund']
                ));
            }
            $this->writeLogFile($notLinked, $output);
        }
        return Command::SUCCESS;
    }

    private function writeLogFile(array $notLinked, OutputInterface $output): void
    {
        $filename = 'schoolkids-unmatched.txt';
        $lines = ["ID\tName\tStreet\tNumber\tZIP\tCity\tGrund"];
        foreach ($notLinked as $fail) {
            $lines[] = implode("\t", [
                $fail['id'],
                $fail['name'],
                $fail['street'] ?? '',
                $fail['houseNumber'] ?? '',
                $fail['zip'] ?? '',
                $fail['city'] ?? '',
                $fail['grund'] ?? ''
            ]);
        }
        file_put_contents($filename, implode("\n", $lines));
        $output->writeln("<comment>Fehlerliste geschrieben nach: $filename</comment>");
    }

    /**
     * Bereinigt eine PLZ wie '46562.0', 'D-46562' usw. auf 5-stellige PLZ.
     */
    private function normalizePostcode(?string $postcode): ?string
    {
        if (!$postcode) return null;
        $postcode = trim($postcode);

        if (is_numeric($postcode)) {
            return str_pad((string)(int)$postcode, 5, "0", STR_PAD_LEFT);
        }
        if (preg_match('/\b(\d{5})\b/', $postcode, $match)) {
            return $match[1];
        }
        $numbersOnly = preg_replace('/\D/', '', $postcode);
        if (strlen($numbersOnly) === 5) return $numbersOnly;
        if (strlen($numbersOnly) > 5) return substr($numbersOnly, 0, 5);
        return null;
    }
}
