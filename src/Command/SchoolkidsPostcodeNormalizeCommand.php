<?php

namespace App\Command;

use App\Entity\Schoolkids;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:schoolkids:postcode-normalize',
    description: 'Normalisiert alle Postleitzahlen in Schoolkids (Excel-Sondermüll zu 5-stelliger PLZ)'
)]
class SchoolkidsPostcodeNormalizeCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(Schoolkids::class);
        $kids = $repo->findAll();

        $fixed = 0;
        $skipped = 0;

        foreach ($kids as $kid) {
            $orig = $kid->getZip();
            $norm = $this->normalizePostcode($orig);

            if ($orig && $norm && $orig !== $norm) {
                $output->writeln(sprintf(
                    'ID %d: "%s" => "%s" <info>(korrigiert)</info>',
                    $kid->getId(),
                    $orig,
                    $norm
                ));
                $kid->setZip($norm);
                $this->em->persist($kid);
                $fixed++;
            } elseif ($orig && !$norm) {
                $output->writeln(sprintf(
                    'ID %d: "%s" <error>(nicht interpretierbar!)</error>',
                    $kid->getId(),
                    $orig
                ));
                $skipped++;
            }
        }

        $this->em->flush();

        $output->writeln("\n<info>Fertig. $fixed PLZs korrigiert. $skipped ungültig/unbearbeitet.</info>");
        return Command::SUCCESS;
    }

    /**
     * Nimmt jeden PLZ-Eintrag und gibt eine bereinigte 5-stellige Zahl zurück (oder null).
     */
    private function normalizePostcode(?string $postcode): ?string
    {
        if (!$postcode) return null;
        $postcode = trim($postcode);

        // Zahl oder "46562.0" → zu "46562"
        if (is_numeric($postcode)) {
            return str_pad((string)(int)$postcode, 5, "0", STR_PAD_LEFT);
        }
        // Finde 5-stellige Zahl in String (z.B. auch "D-46562" oder "46562 xyz")
        if (preg_match('/\b(\d{5})\b/', $postcode, $match)) {
            return $match[1];
        }
        // Nur Ziffern extrahieren, ggf. abschneiden
        $numbersOnly = preg_replace('/\D/', '', $postcode);
        if (strlen($numbersOnly) === 5) {
            return $numbersOnly;
        }
        if (strlen($numbersOnly) > 5) {
            return substr($numbersOnly, 0, 5);
        }
        return null;
    }
}
