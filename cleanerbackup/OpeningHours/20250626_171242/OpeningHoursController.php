<?php

namespace App\Controller;

use App\Entity\OpeningHours;
use App\Form\OpeningHours1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/admin/opening-hours')]
final class OpeningHoursController extends AbstractController
{
    public function __construct()
    {
        // Constructor without MenuService
    }

    #[Route(name: 'app_opening_hours_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $openingHours = $entityManager
            ->getRepository(OpeningHours::class)
            ->findAll();

        return $this->render('opening_hours/index.html.twig', [
            'opening_hours' => $openingHours,
            'page_title' => 'Öffnungszeiten',
        ]);
    }

    #[Route('/new', name: 'app_opening_hours_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $openingHour = new OpeningHours();
        $form = $this->createForm(OpeningHours1Type::class, $openingHour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($openingHour);
            $entityManager->flush();

            return $this->redirectToRoute('app_opening_hours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('opening_hours/new.html.twig', [
            'opening_hour' => $openingHour,
            'form' => $form,
            'page_title' => 'Öffnungszeiten Neu',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_opening_hours_show', methods: ['GET'])]
    public function show(OpeningHours $openingHour): Response
    {
        return $this->render('opening_hours/show.html.twig', [
            'opening_hour' => $openingHour,
            'page_title' => 'Öffnungszeiten',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_opening_hours_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OpeningHours $openingHour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OpeningHours1Type::class, $openingHour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_opening_hours_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('opening_hours/edit.html.twig', [
            'opening_hour' => $openingHour,
            'form' => $form,
            'page_title' => 'Öffnungszeiten',
        ]);
    }
    #[Route('/{id<\d+>}/update', name: 'opening_hours_update', methods: ['POST', 'PUT', 'PATCH'])]
    public function update(Request $request, OpeningHours $openingHours, EntityManagerInterface $em): JsonResponse
    {
        $form = $this->createForm(OpeningHoursType::class, $openingHours);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'Öffnungszeiten gespeichert!']);
        }
    
        return new JsonResponse(['error' => true, 'message' => 'Fehler beim Speichern'], 400);
    }
    
    #[Route('/{id<\d+>}/inline-update', name: 'app_opening_hours_inline_update', methods: ['POST'])]
    public function inlineUpdate(Request $request, OpeningHours $openingHour, EntityManagerInterface $entityManager): Response
    {
        // Parse die JSON-Daten aus der Anfrage
        $data = json_decode($request->getContent(), true);

        // Aktualisiere die Felder, die geändert wurden
        if (isset($data['day'])) {
            $openingHour->setDay($data['day']);
        }
        if (isset($data['morningStart'])) {
            $openingHour->setMorningStart(new \DateTime($data['morningStart']));
        }
        if (isset($data['morningEnd'])) {
            $openingHour->setMorningEnd(new \DateTime($data['morningEnd']));
        }
        if (isset($data['afternoonStart'])) {
            $openingHour->setAfternoonStart(new \DateTime($data['afternoonStart']));
        }
        if (isset($data['afternoonEnd'])) {
            $openingHour->setAfternoonEnd(new \DateTime($data['afternoonEnd']));
        }

        // Speichere die Änderungen in der Datenbank
        $entityManager->flush();

        return $this->json(['status' => 'success'], Response::HTTP_OK);
    }

    #[Route('/{id<\d+>}/edit/ajax', name: 'app_opening_hours_edit_ajax', methods: ['POST'])]
    public function editAjax(Request $request, OpeningHours $openingHour, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        // Funktion zur sicheren Umwandlung von Zeitwerten oder NULL
        function parseTimeOrNull($time)
        {
            if ($time === null || $time === '' || strtolower($time) === 'geschlossen') {
                return null; // NULL in der Datenbank speichern
            }
    
            try {
                return new \DateTime($time);
            } catch (\Exception $e) {
                return null; // Fehlerhafte Werte ignorieren und NULL speichern
            }
        }
    
        // Werte setzen, wobei "00:00" als gültige Zeit gespeichert wird
        $openingHour->setMorningStart(parseTimeOrNull($data['morningStart'] ?? null));
        $openingHour->setMorningEnd(parseTimeOrNull($data['morningEnd'] ?? null));
        $openingHour->setAfternoonStart(parseTimeOrNull($data['afternoonStart'] ?? null));
        $openingHour->setAfternoonEnd(parseTimeOrNull($data['afternoonEnd'] ?? null));
    
        $entityManager->persist($openingHour);
        $entityManager->flush();
    
        return new JsonResponse([
            'status' => 'success',
            'updatedData' => [
                'morningStart' => $openingHour->getMorningStart() ? $openingHour->getMorningStart()->format('H:i') : 'geschlossen',
                'morningEnd' => $openingHour->getMorningEnd() ? $openingHour->getMorningEnd()->format('H:i') : 'geschlossen',
                'afternoonStart' => $openingHour->getAfternoonStart() ? $openingHour->getAfternoonStart()->format('H:i') : 'geschlossen',
                'afternoonEnd' => $openingHour->getAfternoonEnd() ? $openingHour->getAfternoonEnd()->format('H:i') : 'geschlossen',
            ],
        ]);
    }
    

    #[Route('/{id<\d+>}', name: 'app_opening_hours_delete', methods: ['POST'])]
    public function delete(Request $request, OpeningHours $openingHour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$openingHour->getId(), $request->request->get('_token'))) {
            $entityManager->remove($openingHour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_opening_hours_index', [], Response::HTTP_SEE_OTHER);
    }
}
