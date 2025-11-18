<?php

namespace App\Command;

use App\Entity\School;
use App\Entity\Schoolkids;
use App\Entity\ContactPerson;
use App\Entity\Auftraggeber;
use App\Entity\Employee;
use App\Repository\OfficialAddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(
    name: 'app:official-address:sync-all',
    description: 'Verknüpft passende OfficialAddress-Datensätze mit School, Schoolkids, ContactPerson, Auftraggeber, Employee'
)]
class SyncAllOfficialAddressesCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OfficialAddressRepository $addressRepo,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
{
    $mapping = [
        'School'        => [School::class,        ['street' => 'getStreet',    'houseNumber' => 'getStreetNo',      'postcode' => 'getZip',      'city' => 'getCity']],
        'Schoolkids'    => [Schoolkids::class,    ['street' => 'getStreet',    'houseNumber' => 'getStreetNumber',  'postcode' => 'getZip',      'city' => 'getCity']],
        'ContactPerson' => [ContactPerson::class, ['street' => 'getStreet',    'houseNumber' => 'getStreetNumber',  'postcode' => 'getZip',      'city' => 'getCity']],
        'Auftraggeber'  => [Auftraggeber::class,  ['street' => 'getStrasse',   'houseNumber' => 'getStrasseNr',     'postcode' => 'getPlz',      'city' => 'getStadt']],
        'Employee'      => [Employee::class,      ['street' => 'getPSStrasse', 'houseNumber' => null,               'postcode' => 'getPSPlzOrt', 'city' => null]],
    ];

    $helper = $this->getHelper('question');

    $choices = array_keys($mapping);
$choices[] = 'Alle (alle Entitäten)';
$choices[] = 'Abbrechen (nichts tun)';

$question = new ChoiceQuestion(
    'Welche Entität(en) sollen gepatcht werden? (Mehrfachauswahl mit Komma, z.B. "School,Employee")',
    $choices
);
$question->setErrorMessage('Ungültige Auswahl.');
$question->setMultiselect(true);

$selected = $helper->ask($input, $output, $question);

if (in_array('Abbrechen (nichts tun)', $selected)) {
    $output->writeln('<comment>Abbruch durch Benutzer.</comment>');
    return Command::SUCCESS;
}

if (in_array('Alle (alle Entitäten)', $selected)) {
    $entitiesToProcess = $mapping;
} else {
    $entitiesToProcess = array_filter(
        $mapping,
        fn($label) => in_array($label, $selected),
        ARRAY_FILTER_USE_KEY
    );
}


    $total = 0;
    $notPatched = []; // <--- HIER sammeln wir alle fehlenden

    foreach ($entitiesToProcess as $label => [$entityClass, $fields]) {
        $items = $this->em->getRepository($entityClass)->findAll();
        $count = 0;

        foreach ($items as $item) {
            $street = $fields['street'] ? $item->{$fields['street']}() : null;
$houseNumber = $fields['houseNumber'] ? $item->{$fields['houseNumber']}() : null;
$postcode = $fields['postcode'] ? $item->{$fields['postcode']}() : null;
$city = $fields['city'] ? $item->{$fields['city']}() : null;

// --- PLZ bereinigen ---
if ($postcode) {
    // Wandelt alles wie "46562.0", "46562 ", " D-46562" in "46562" um
    if (is_numeric($postcode)) {
        $postcode = str_pad((string)(int)$postcode, 5, "0", STR_PAD_LEFT);
    } else {
        // Extrahiere die erste 5-stellige Zahl (z.B. auch bei "D-46562" oder "46562.0")
        if (preg_match('/\b(\d{5})\b/', $postcode, $match)) {
            $postcode = $match[1];
        } else {
            // Ansonsten einfach alles, was nicht Zahl ist, rauswerfen
            $postcode = preg_replace('/\D/', '', $postcode);
            if (strlen($postcode) > 5) {
                $postcode = substr($postcode, 0, 5);
            }
        }
    }
}

            $official = $this->addressRepo->findOneBy([
                'street' => $street,
                'postcode' => $postcode,
                'city' => $city,
                'houseNumber' => $houseNumber,
            ]);

            if ($official) {
                $item->setAddress($official);
                $this->em->persist($item);
                $count++;
                $output->writeln("<info>[$label] ID {$item->getId()}: Adresse gesetzt</info>");
            } else {
                $output->writeln("<error>[$label] ID {$item->getId()}: Keine passende Adresse gefunden</error>");
                $notPatched[] = [
                    'entity' => $label,
                    'id' => $item->getId(),
                    'street' => $street,
                    'houseNumber' => $houseNumber,
                    'postcode' => $postcode,
                    'city' => $city,
                    'grund' => 'nicht gefunden'
                ];
            }
        }

        $output->writeln("<comment>$count Datensätze aus $label aktualisiert.</comment>");
        $total += $count;
    }

    $this->em->flush();
    $output->writeln("<info>Gesamt: $total Adressen verknüpft.</info>");

    // NEU: Am Ende Übersicht aller nicht gepatchten ausgeben
    if (!empty($notPatched)) {
        $output->writeln("\n<error>Nicht gepatchte Datensätze (fehlende PLZ/Adresse):</error>");
        foreach ($notPatched as $fail) {
            $output->writeln(sprintf(
                "[%s] ID %d: %s %s, %s %s (%s)",
                $fail['entity'],
                $fail['id'],
                $fail['street'] ?? '-',
                $fail['houseNumber'] ?? '',
                $fail['postcode'] ?? '-',
                $fail['city'] ?? '-',
                $fail['grund']
            ));
        }
        $output->writeln("<comment>Summe nicht gepatcht: ".count($notPatched)."</comment>");
    }

    return Command::SUCCESS;
}

}
