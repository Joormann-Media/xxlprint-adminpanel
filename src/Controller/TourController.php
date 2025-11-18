<?php

namespace App\Controller;

use App\Entity\Tour;
use App\Entity\TourSchedule;
use App\Form\TourType;
use App\Repository\TourRepository;
use App\Repository\SchoolkidsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tour')]
final class TourController extends AbstractController
{
    #[Route(name: 'app_tour_index', methods: ['GET'])]
    public function index(TourRepository $tourRepository): Response
    {
        return $this->render('tour/index.html.twig', [
            'tours' => $tourRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tour_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        SchoolkidsRepository $schoolkidsRepository
    ): Response
    {
        $tour = new Tour();

        // Default-Schedule anhängen
        if ($tour->getSchedules()->isEmpty()) {
            $schedule = new TourSchedule();
            $tour->addSchedule($schedule);
        }

        $form = $this->createForm(TourType::class, $tour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($tour->getSchedules() as $schedule) {
                $schedule->setTour($tour);
            }

            // --- Automatische Zuordnung: Wenn keine Kids explizit gewählt, alle Kinder der Schule zuweisen!
            if ($tour->getSchool() && $tour->getSchoolkids()->isEmpty()) {
                $kids = $schoolkidsRepository->findBy(['school' => $tour->getSchool()]);
                foreach ($kids as $kid) {
                    $tour->addSchoolkid($kid);
                }
            }

            $entityManager->persist($tour);
            $entityManager->flush();

            return $this->redirectToRoute('app_tour_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tour/new.html.twig', [
            'tour' => $tour,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_tour_show', methods: ['GET'])]
    public function show(Tour $tour): Response
    {
        return $this->render('tour/show.html.twig', [
            'tour' => $tour,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_tour_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Tour $tour, 
        EntityManagerInterface $entityManager, 
        SchoolkidsRepository $schoolkidsRepository
    ): Response
    {
        $form = $this->createForm(TourType::class, $tour, [
            // **Hack:** Schule schon bekannt, Kids-Auswahl kann via QueryBuilder gefiltert werden!
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($tour->getSchedules() as $schedule) {
                $schedule->setTour($tour);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_tour_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tour/edit.html.twig', [
            'tour' => $tour,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_tour_delete', methods: ['POST'])]
    public function delete(Request $request, Tour $tour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tour->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tour_index', [], Response::HTTP_SEE_OTHER);
    }
}
