<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\LicenceClass;
use App\Entity\User;
use App\Entity\ContactPerson;
use App\Entity\OfficialAddress;


#[Route('/employee')]
final class EmployeeController extends AbstractController
{
    #[Route(name: 'app_employee_index', methods: ['GET'])]
public function index(
    Request $request,
    EmployeeRepository $employeeRepository
): Response {
    $search    = $request->query->get('search', '');
    $sort      = $request->query->get('sort', 'lastName');
    $direction = strtoupper($request->query->get('direction', 'ASC'));
    $limit     = (int)$request->query->get('limit', 50);
    $page      = max(1, (int)$request->query->get('page', 1));

    // Mapping für Sortierung
    $sortMap = [
        'lastName'       => 'e.lastName',
        'firstName'      => 'e.firstName',
        'isDriver'       => 'e.isDriver',
        'phone'          => 'e.phone',
        'email'          => 'e.email',
        'employeeNumber' => 'e.employeeNumber',
        'birthDate'      => 'e.birthDate',
        'licenceClass'   => 'lc.shortName',
        'costCenter'     => 'cc.name',
        'company'        => 'c.companyname',
        // KEIN 'fullAddress', sondern einzelne Teile
        'address'        => 'a.street', // Default: Straße als Sortierung
    ];
    $allowedSort = array_keys($sortMap);

    if (!in_array($sort, $allowedSort, true)) {
        $sort = 'lastName';
    }
    if (!in_array($direction, ['ASC', 'DESC'], true)) {
        $direction = 'ASC';
    }

    $qb = $employeeRepository->createQueryBuilder('e')
        ->leftJoin('e.company', 'c')->addSelect('c')
        ->leftJoin('e.costCenters', 'cc')->addSelect('cc')
        ->leftJoin('e.licenceClasses', 'lc')->addSelect('lc')
        ->leftJoin('e.address', 'a')->addSelect('a');

    // Suche (achte auf echte Felder, KEINE virtuellen Getter)
    if ($search) {
        $needle = '%' . mb_strtolower($search) . '%';
        $qb->andWhere(
            'LOWER(e.lastName) LIKE :needle
            OR LOWER(e.firstName) LIKE :needle
            OR LOWER(e.phone) LIKE :needle
            OR LOWER(e.email) LIKE :needle
            OR LOWER(e.employeeNumber) LIKE :needle
            OR LOWER(c.companyname) LIKE :needle
            OR LOWER(a.street) LIKE :needle
            OR LOWER(a.houseNumber) LIKE :needle
            OR LOWER(a.postcode) LIKE :needle
            OR LOWER(a.city) LIKE :needle
            OR LOWER(a.district) LIKE :needle
            OR LOWER(cc.name) LIKE :needle
            OR LOWER(lc.shortName) LIKE :needle'
        )->setParameter('needle', $needle);
    }

    // Gruppieren, damit bei M:N keine Dubletten (wegen CostCenter/LicenceClass)
    $qb->groupBy('e.id');

    // Sortierung
    $qb->orderBy($sortMap[$sort], $direction);

    $pager = new \Pagerfanta\Pagerfanta(new \Pagerfanta\Doctrine\ORM\QueryAdapter($qb));
    $pager->setMaxPerPage($limit);
    $pager->setCurrentPage($page);

    $template = $request->isXmlHttpRequest()
        ? 'employee/_table.html.twig'
        : 'employee/index.html.twig';

    return $this->render($template, [
        'pagination' => $pager,
        'employees'  => $pager->getCurrentPageResults(),
        'search'     => $search,
        'sort'       => $sort,
        'direction'  => $direction,
        'limit'      => $limit,
        'route'      => $request->attributes->get('_route'),
        'query'      => $request->query->all(),
        'page_title' => 'Mitarbeiterübersicht',
    ]);
}




    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        // Adresse vorbereiten (für das Select2-Feld)
        $selectedAddress = null;
        $addressData = $form->get('address')->getData();
        if ($addressData) {
            // DataTransformer setzt bereits die Entity!
            if ($addressData instanceof OfficialAddress) {
                $selectedAddress = $addressData;
            } elseif (is_numeric($addressData)) {
                $selectedAddress = $entityManager->getRepository(OfficialAddress::class)->find($addressData);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
            'selectedAddress' => $selectedAddress, // NEU!
        ]);
    }

    #[Route('/import', name: 'app_employee_import', methods: ['POST'])]
public function import(Request $request, EntityManagerInterface $em): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!is_array($data)) {
        return new JsonResponse(['error' => 'Invalid JSON. Expected array of objects.'], 400);
    }

    $imported = 0;
    $skipped = 0;
    $skippedDetails = [];

    // Mapping: JSON-Property => [Setter, Typ ('string','int','float','bool','date')]
    $mapping = [
        // Hauptfelder
        'firstName'              => ['setFirstName', 'string'],
        'lastName'               => ['setLastName', 'string'],
        'email'                  => ['setEmail', 'string'],
        'phone'                  => ['setPhone', 'string'],
        'employeeNumber'         => ['setEmployeeNumber', 'string'],
        'costCenter'             => ['setCostCenter', 'string'],
        'hiredAt'                => ['setHiredAt', 'date'],
        'leftAt'                 => ['setLeftAt', 'date'],
        'vacationDays'           => ['setVacationDays', 'int'],
        'vacationDaysExtra'      => ['setVacationDaysExtra', 'int'],
        'agreedHoursDaily'       => ['setAgreedHoursDaily', 'float'],
        'agreedHoursWeekly'      => ['setAgreedHoursWeekly', 'float'],
        'agreedHoursMonthly'     => ['setAgreedHoursMonthly', 'float'],
        'isDriver'               => ['setIsDriver', 'bool'],

        // Excel/PS Felder (ohne Umlaute!)
        'PSFahrernummer'         => ['setPSFahrernummer', 'string'],
        'PSAnrede'               => ['setPSAnrede', 'string'],
        'PSName_1'               => ['setPSName1', 'string'],
        'PSName_2'               => ['setPSName2', 'string'],
        'PSSuchwort'             => ['setPSSuchwort', 'string'],
        'KUT'                    => ['setKUT', 'string'],
        'PSStrasse'              => ['setPSStrasse', 'string'],
        'PSPlzOrt'               => ['setPSPlzOrt', 'string'],
        'PSGeburtsdatum'         => ['setPSGeburtsdatum', 'date'],
        'PSBeschaeftigungsart'   => ['setPSBeschaeftigungsart', 'string'],
        'PSTaetigkeit'           => ['setPSTaetigkeit', 'string'],
        'PSEintrittsdatum'       => ['setPSEintrittsdatum', 'date'],
        'FSZbefristetbis'        => ['setFSZbefristetbis', 'date'],
        'PSAusgeschieden'        => ['setPSAusgeschieden', 'bool'],
        'PSAustrittsdatum'       => ['setPSAustrittsdatum', 'date'],
        'PSEMail'                => ['setPSEMail', 'string'],
        'PSTelefon'              => ['setPSTelefon', 'string'],
        'PSHandy'                => ['setPSHandy', 'string'],
        'PSfaehrt_KINDERTOUREN'  => ['setPSfaehrtKINDERTOUREN', 'bool'],
        'PSFahrerkuerzel'        => ['setPSFahrerkuerzel', 'string'],
        'PSPSchein_gueltig_bis'  => ['setPSPScheinGueltigBis', 'date'],
        'PSBusbegleitung_oFS'    => ['setPSBusbegleitungOFS', 'bool'],
        'PSBusbegleitung_mFS'    => ['setPSBusbegleitungMFS', 'bool'],
        'PSfaehrt_Schulbus'      => ['setPSfaehrtSchulbus', 'bool'],
        'PSfaehrt_TAXI'          => ['setPSfaehrtTAXI', 'bool'],
        'PSBuero'                => ['setPSBuero', 'bool'],
        'PSFunk'                 => ['setPSFunk', 'bool'],
        'PSFS_D1'                => ['setPSFSD1', 'bool'],
        'PSFS_D'                 => ['setPSFSD', 'bool'],
        'PSFS_DE'                => ['setPSFSDE', 'bool'],
        'FSZvertragbefristet'    => ['setFSZvertragbefristet', 'bool'],
        'PSBus_gueltig_bis'      => ['setPSBusGueltigBis', 'date'],
        'PSTaxi_gueltig_bis'     => ['setPSTaxiGueltigBis', 'date'],
    ];

    foreach ($data as $index => $entry) {
        $errors = [];

        // Pflichtfelder checken
        if (empty($entry['firstName']) || empty($entry['lastName'])) {
            $errors[] = "First name or last name missing";
        }

        // Lizenzklassen holen (optional)
        $licenceClasses = [];
        if (!empty($entry['licenceClasses']) && is_array($entry['licenceClasses'])) {
            foreach ($entry['licenceClasses'] as $licenceClassId) {
                $lc = $em->getRepository(\App\Entity\LicenceClass::class)->find($licenceClassId);
                if ($lc) {
                    $licenceClasses[] = $lc;
                } else {
                    $errors[] = "Invalid licence class ID: $licenceClassId";
                }
            }
        }

        // EmergencyContact (optional)
        $emergencyContact = null;
        if (!empty($entry['emergencyContact'])) {
            $emergencyContact = $em->getRepository(\App\Entity\ContactPerson::class)->find($entry['emergencyContact']);
            if (!$emergencyContact) {
                $errors[] = "Invalid emergencyContact ID: {$entry['emergencyContact']}";
            }
        }

        // User (optional)
        $user = null;
        if (!empty($entry['user'])) {
            $user = $em->getRepository(\App\Entity\User::class)->find($entry['user']);
            if (!$user) {
                $errors[] = "Invalid user ID: {$entry['user']}";
            }
        }

        // Fehler = skip!
        if ($errors) {
            $skipped++;
            $skippedDetails[] = [
                'index' => $index,
                'errors' => $errors,
                'name' => ($entry['firstName'] ?? '???') . ' ' . ($entry['lastName'] ?? '???')
            ];
            continue;
        }

        $employee = new Employee();

        // Setze alle Felder nach Mapping
        foreach ($mapping as $field => [$setter, $type]) {
            if (!array_key_exists($field, $entry)) {
                continue;
            }
            $value = $entry[$field];

            if ($type === 'date' && !empty($value)) {
                try {
                    $value = new \DateTime($value);
                } catch (\Exception $e) {
                    $value = null;
                }
            }
            if ($type === 'int') {
                $value = ($value === '' || $value === null) ? null : (int)$value;
            }
            if ($type === 'float') {
                $value = ($value === '' || $value === null) ? null : (float)$value;
            }
            if ($type === 'bool') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }

            if (method_exists($employee, $setter)) {
                $employee->$setter($value);
            }
        }

        // Relationen setzen
        $employee->setEmergencyContact($emergencyContact);
        $employee->setUser($user);

        foreach ($licenceClasses as $licenceClass) {
            $employee->addLicenceClass($licenceClass);
        }

        $em->persist($employee);
        $imported++;
    }

    $em->flush();

    return new JsonResponse([
        'imported' => $imported,
        'skipped' => $skipped,
        'skippedDetails' => $skippedDetails
    ]);
}

    #[Route('/{id<\d+>}', name: 'app_employee_show', methods: ['GET'])]
    public function show(Employee $employee): Response
    {
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        // Adresse vorbereiten (für das Select2-Feld)
        $selectedAddress = null;
        $addressData = $form->get('address')->getData();
        if ($addressData) {
            if ($addressData instanceof OfficialAddress) {
                $selectedAddress = $addressData;
            } elseif (is_numeric($addressData)) {
                $selectedAddress = $entityManager->getRepository(OfficialAddress::class)->find($addressData);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
            'selectedAddress' => $selectedAddress, // NEU!
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_employee_delete', methods: ['POST'])]
    public function delete(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_employee_index', [], Response::HTTP_SEE_OTHER);
    }
}
