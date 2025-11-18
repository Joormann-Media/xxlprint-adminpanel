<?php
namespace App\Command;

use App\Entity\Employee;
use App\Entity\OfficialAddress;
use App\Repository\EmployeeRepository;
use App\Repository\OfficialAddressRepository;
use App\Repository\CostCenterRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'import:employee:excel',
    description: 'Importiert und aktualisiert Mitarbeiter aus public/employeecounter.xlsx mit CostCenter-Mapping anhand PSTätigkeit.',
)]
class ImportEmployeeExcelCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private EmployeeRepository $employeeRepo,
        private OfficialAddressRepository $addressRepo,
        private CostCenterRepository $costCenterRepo
    ) {
        parent::__construct();
    }

    // Hilfsfunktion für Excel-Jahr-Parsing
    private function fixExcelYear(string $date): string
    {
        // Mapping deutscher Monatsnamen auf englische
        $months = [
            'Januar' => 'Jan', 'Jan' => 'Jan',
            'Februar' => 'Feb', 'Feb' => 'Feb',
            'März' => 'Mar', 'Mär' => 'Mar', 'Mrz' => 'Mar',
            'April' => 'Apr', 'Apr' => 'Apr',
            'Mai' => 'May',
            'Juni' => 'Jun', 'Jun' => 'Jun',
            'Juli' => 'Jul', 'Jul' => 'Jul',
            'August' => 'Aug', 'Aug' => 'Aug',
            'September' => 'Sep', 'Sep' => 'Sep',
            'Oktober' => 'Oct', 'Okt' => 'Oct',
            'November' => 'Nov', 'Nov' => 'Nov',
            'Dezember' => 'Dec', 'Dez' => 'Dec'
        ];

        // Monatsnamen ersetzen
        foreach ($months as $de => $en) {
            $date = preg_replace('/\b' . $de . '\b/u', $en, $date);
        }

        // Zweistelliges Jahr hinten ersetzen
        $date = preg_replace_callback('/(\d{2}[.\-][A-Za-z]{3}[.\-])(\d{2})$/u', function($m) {
            $yy = (int)$m[2];
            $yyyy = ($yy >= 0 && $yy <= 25) ? (2000 + $yy) : (1900 + $yy);
            return $m[1] . $yyyy;
        }, $date);

        // DD.MM.YY oder DD.MM.YYYY
        $date = preg_replace_callback('/(\d{2}[.\-]\d{2}[.\-])(\d{2})$/', function($m) {
            $yy = (int)$m[2];
            $yyyy = ($yy >= 0 && $yy <= 25) ? (2000 + $yy) : (1900 + $yy);
            return $m[1] . $yyyy;
        }, $date);

        return $date;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = __DIR__ . '/../../public/employeecounter.xlsx';
        if (!file_exists($filePath)) {
            $output->writeln("<error>Datei nicht gefunden: $filePath</error>");
            return Command::FAILURE;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $header = array_shift($rows);
        $colMap = array_flip($header);

        // Mapping Excel-Wert → CostCenter-Code/ID (du kannst bei neuen Departments einfach ergänzen)
        $costCenterMap = [
            'Büro'       => 'buero',
            'Bus'        => 'bus',
            'Reinigung'  => 'renigung',
            'Kindertour' => 'kindertourfahrer',
            'Taxi'       => 'taxi',
            'Werkstatt'  => 'werkstatt',
            'Begleitung' => 'kindertourbegleiter',
        ];

        $imported = 0; $updated = 0; $skipped = 0;

        foreach ($rows as $row) {
            $employeeNumber = trim($row[$colMap['PSFahrernummer']] ?? '');
            if (!$employeeNumber) {
                $skipped++;
                continue;
            }

            $employee = $this->employeeRepo->findOneBy(['employeeNumber' => $employeeNumber]);
            $isNew = false;
            if (!$employee) {
                $employee = new Employee();
                $employee->setEmployeeNumber($employeeNumber);
                $isNew = true;
            }

            // Helper zum Updaten (nur wenn sich was ändert)
            $setIfChanged = function($getter, $setter, $newVal) use ($employee) {
                if (method_exists($employee, $getter) && method_exists($employee, $setter)) {
                    $oldVal = $employee->$getter();
                    if ($oldVal != $newVal) $employee->$setter($newVal);
                }
            };

            $setIfChanged('getFirstName', 'setFirstName', $row[$colMap['PSName_2']] ?? '');
            $setIfChanged('getLastName', 'setLastName', $row[$colMap['PSName_1']] ?? '');
            $setIfChanged('getEmail', 'setEmail', $row[$colMap['PSEMail']] ?? null);

            // Telefonnummern kombinieren
            $telefon = trim($row[$colMap['PSTelefon']] ?? '');
            $handy   = trim($row[$colMap['PSHandy']] ?? '');
            $phones = [];
            if ($telefon) $phones[] = $telefon;
            if ($handy && $handy !== $telefon) $phones[] = $handy;
            $phoneField = implode(' / ', $phones);
            $setIfChanged('getPhone', 'setPhone', $phoneField);

            // isDriver
            $isDriver = strtoupper(trim($row[$colMap['PSfährt_TAXI']] ?? '')) === 'WAHR';
            $setIfChanged('isDriver', 'setIsDriver', $isDriver);

            // Geburtstdatum (mit robustem Excel/Monats/YEAR Fix)
            $birth = trim($row[$colMap['PSGeburtsdatum']] ?? '');
            if ($birth) {
                $birthFixed = $this->fixExcelYear($birth);
                $date = \DateTime::createFromFormat('d-M-Y', $birthFixed)
                    ?: \DateTime::createFromFormat('d.m.Y', $birthFixed)
                    ?: @new \DateTime($birthFixed);
                $setIfChanged('getBirthDate', 'setBirthDate', $date ?: null);
            }

            $setIfChanged('getShortCode', 'setShortCode', $row[$colMap['PSFahrerkürzel']] ?? null);

            // hiredAt
            $hire = trim($row[$colMap['PSEintrittsdatum']] ?? '');
            if ($hire) {
                $hireFixed = $this->fixExcelYear($hire);
                $date = \DateTime::createFromFormat('d-M-Y', $hireFixed)
                    ?: \DateTime::createFromFormat('d.m.Y', $hireFixed)
                    ?: @new \DateTime($hireFixed);
                $setIfChanged('getHiredAt', 'setHiredAt', $date ?: null);
            }

            // === CostCenter nach PSTätigkeit ===
            // Erst alle CostCenters rauswerfen (sauberer Import!)
            foreach ($employee->getCostCenters() as $existing) {
                $employee->removeCostCenter($existing);
            }
            $costCenterUsed = null;
            $tätigkeit = trim($row[$colMap['PSTätigkeit']] ?? '');
            $costCenterCode = $costCenterMap[$tätigkeit] ?? null;
            if ($costCenterCode) {
                $costCenter = $this->costCenterRepo->findOneBy(['code' => $costCenterCode]);
                if ($costCenter) {
                    $employee->addCostCenter($costCenter);
                    $costCenterUsed = $costCenter->getName();
                }
            }
            if (!$costCenterUsed) {
                $costCenterUsed = '[UNMAPPED: ' . $tätigkeit . ']';
            }

            // Adresse
            $street = trim($row[$colMap['PSStraße']] ?? '');
            $plzOrt = trim($row[$colMap['PSPlzOrt']] ?? '');
            $postcode = ''; $city = '';
            if (preg_match('/^(\d{5})\s*(.*)$/', $plzOrt, $m)) {
                $postcode = $m[1]; $city = $m[2];
            }
            if ($street && $postcode && $city) {
                $address = $this->addressRepo->findOneBy([
                    'street' => $street,
                    'postcode' => $postcode,
                    'city' => $city,
                ]);
                if (!$address) {
                    $address = new OfficialAddress();
                    $address->setStreet($street)
                        ->setPostcode($postcode)
                        ->setCity($city);
                    $this->em->persist($address);
                }
                if ($employee->getAddress() !== $address) {
                    $employee->setAddress($address);
                }
            }

            // Company leer (optional später)
            $employee->setCompany(null);

            $this->em->persist($employee);

            if ($isNew) $imported++; else $updated++;

            // Debug-Ausgabe
            $output->writeln(sprintf(
                '[%s] %s %s: %s | CostCenter: %s | Geburtsdatum: %s',
                $employee->getEmployeeNumber(),
                $employee->getLastName(),
                $employee->getFirstName(),
                $isNew ? 'NEU' : 'UPDATE',
                $costCenterUsed,
                $employee->getBirthDate()?->format('d.m.Y')
            ));
        }
        $this->em->flush();

        $output->writeln("<info>Import fertig! $imported NEU, $updated aktualisiert, $skipped übersprungen.</info>");
        return Command::SUCCESS;
    }
}
