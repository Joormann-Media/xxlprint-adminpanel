<?php

namespace App\Controller;

use App\Entity\Holiday;
use App\Form\HolidayType;
use App\Repository\HolidayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/holiday')]
final class HolidayController extends AbstractController
{
    #[Route(name: 'app_holiday_index', methods: ['GET'])]
    public function index(HolidayRepository $holidayRepository): Response
    {
        return $this->render('holiday/index.html.twig', [
            'holidays' => $holidayRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_holiday_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $holiday = new Holiday();
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($holiday);
            $entityManager->flush();

            return $this->redirectToRoute('app_holiday_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('holiday/new.html.twig', [
            'holiday' => $holiday,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_holiday_show', methods: ['GET'])]
    public function show(Holiday $holiday): Response
    {
        return $this->render('holiday/show.html.twig', [
            'holiday' => $holiday,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_holiday_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Holiday $holiday, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HolidayType::class, $holiday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_holiday_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('holiday/edit.html.twig', [
            'holiday' => $holiday,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_holiday_delete', methods: ['POST'])]
    public function delete(Request $request, Holiday $holiday, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$holiday->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($holiday);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_holiday_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/import', name: 'app_holiday_import', methods: ['GET', 'POST'])]
public function import(Request $request, EntityManagerInterface $em, HolidayRepository $holidayRepo): Response
{
    // Bundesland-Auswahl
    $states = ['BW','BY','BE','BB','HB','HH','HE','MV','NI','NW','RP','SL','SN','ST','SH','TH'];
    $defaultYear = (int)date('Y');

    if ($request->isMethod('POST')) {
        $type = $request->request->get('type'); // holiday | vacation
        $stateCode = $request->request->get('state');
        $year = (int)$request->request->get('year', $defaultYear);

        $imported = 0;
        $skipped = 0;
        $failed = 0;

        // State-Entity vorab holen (Performance)
        $stateEntity = $em->getRepository(\App\Entity\State::class)->findOneBy(['code'=>$stateCode]);
        if (!$stateEntity) {
            $this->addFlash('danger', "Kein Bundesland mit dem Kürzel '$stateCode' gefunden.");
            return $this->redirectToRoute('app_holiday_import');
        }

        if ($type === 'holiday') {
            $url = "https://feiertage-api.de/api/?jahr=$year&nur_land=$stateCode&nur_daten=1";
            $json = @file_get_contents($url);
            $data = json_decode($json, true);

            if (!is_array($data)) {
                $this->addFlash('danger', 'Fehler beim Abruf der Feiertage-API.');
                return $this->redirectToRoute('app_holiday_import');
            }

            // Die API liefert ein Array mit Feiertagen als Werte, nicht als Key-Value-Paare
            foreach ($data as $name => $f) {
                if (empty($f['datum'])) { $failed++; continue; }
                try {
                    $date = new \DateTime($f['datum']);
                } catch (\Exception $e) {
                    $failed++; continue;
                }
                $exists = $holidayRepo->findOneBy(['name' => $name, 'startDate' => $date]);
                if ($exists) { $skipped++; continue; }

                $holiday = new Holiday();
                $holiday->setName($name);
                $holiday->setType('holiday');
                $holiday->setStartDate($date);
                $holiday->setEndDate($date);
                $holiday->setRecurrence('yearly');
                $holiday->setComment('Autoimport: feiertage-api.de' . (!empty($f['hinweis']) ? " ({$f['hinweis']})" : ''));
                $holiday->setState($stateEntity);
                $em->persist($holiday);
                $imported++;
            }
        } else { // Schulferien
            $url = "https://ferien-api.de/api/v1/holidays/DE/$stateCode/$year";
            $json = @file_get_contents($url);
            $data = json_decode($json, true);

            // Die API liefert aktuell (Stand jetzt) 404! Also Fehlerhandling:
            if (!is_array($data) || isset($data['status'])) {
                $this->addFlash('danger', 'Fehler beim Abruf der Ferien-API (vermutlich keine Daten vorhanden).');
                return $this->redirectToRoute('app_holiday_import');
            }

            foreach ($data as $ferientyp => $list) {
                if (!is_array($list)) { $failed++; continue; }
                foreach ($list as $f) {
                    if (empty($f['name']) || empty($f['start']) || empty($f['end'])) { $failed++; continue; }
                    try {
                        $start = new \DateTime($f['start']);
                        $end = new \DateTime($f['end']);
                    } catch (\Exception $e) {
                        $failed++; continue;
                    }
                    // Duplikate prüfen
                    $exists = $holidayRepo->findOneBy([
                        'name' => $f['name'],
                        'startDate' => $start,
                        'endDate' => $end,
                    ]);
                    if ($exists) { $skipped++; continue; }

                    $holiday = new Holiday();
                    $holiday->setName($f['name']);
                    $holiday->setType('school_vacation');
                    $holiday->setStartDate($start);
                    $holiday->setEndDate($end);
                    $holiday->setRecurrence('none');
                    $holiday->setComment('Autoimport: ferien-api.de');
                    $holiday->setState($stateEntity);
                    $em->persist($holiday);
                    $imported++;
                }
            }
        }
        $em->flush();

        $this->addFlash('success', "$imported importiert, $skipped übersprungen, $failed fehlgeschlagen.");
        return $this->redirectToRoute('app_holiday_index');
    }

    // GET: Import-Formular anzeigen
    return $this->render('holiday/import.html.twig', [
        'states' => $states,
        'defaultYear' => $defaultYear,
    ]);
}


}
