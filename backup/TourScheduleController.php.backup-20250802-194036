<?php

namespace App\Controller;

use App\Entity\TourSchedule;
use App\Form\TourScheduleType;
use App\Repository\TourScheduleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tour/schedule')]
final class TourScheduleController extends AbstractController
{
    #[Route(name: 'app_tour_schedule_index', methods: ['GET'])]
    public function index(TourScheduleRepository $tourScheduleRepository): Response
    {
        return $this->render('tour_schedule/index.html.twig', [
            'tour_schedules' => $tourScheduleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tour_schedule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tourSchedule = new TourSchedule();
        $form = $this->createForm(TourScheduleType::class, $tourSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tourSchedule);
            $entityManager->flush();

            return $this->redirectToRoute('app_tour_schedule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tour_schedule/new.html.twig', [
            'tour_schedule' => $tourSchedule,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_tour_schedule_show', methods: ['GET'])]
    public function show(TourSchedule $tourSchedule): Response
    {
        return $this->render('tour_schedule/show.html.twig', [
            'tour_schedule' => $tourSchedule,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_tour_schedule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TourSchedule $tourSchedule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TourScheduleType::class, $tourSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tour_schedule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tour_schedule/edit.html.twig', [
            'tour_schedule' => $tourSchedule,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_tour_schedule_delete', methods: ['POST'])]
    public function delete(Request $request, TourSchedule $tourSchedule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tourSchedule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tourSchedule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tour_schedule_index', [], Response::HTTP_SEE_OTHER);
    }
}
