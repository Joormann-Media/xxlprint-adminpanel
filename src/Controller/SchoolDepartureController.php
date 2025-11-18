<?php

namespace App\Controller;

use App\Entity\SchoolDeparture;
use App\Form\SchoolDepartureType;
use App\Repository\SchoolDepartureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/school/departure')]
final class SchoolDepartureController extends AbstractController
{
    #[Route(name: 'app_school_departure_index', methods: ['GET'])]
    public function index(SchoolDepartureRepository $repository): Response
    {
        return $this->render('school_departure/index.html.twig', [
            'schoolDepartures' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_school_departure_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schoolDeparture = new SchoolDeparture();
        $form = $this->createForm(SchoolDepartureType::class, $schoolDeparture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($schoolDeparture);
            $entityManager->flush();

            return $this->redirectToRoute('app_school_departure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('school_departure/new.html.twig', [
            'school_departure' => $schoolDeparture,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_school_departure_show', methods: ['GET'])]
    public function show(SchoolDeparture $schoolDeparture): Response
    {
        return $this->render('school_departure/show.html.twig', [
            'school_departure' => $schoolDeparture,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_school_departure_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SchoolDeparture $schoolDeparture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SchoolDepartureType::class, $schoolDeparture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_school_departure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('school_departure/edit.html.twig', [
            'school_departure' => $schoolDeparture,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_school_departure_delete', methods: ['POST'])]
    public function delete(Request $request, SchoolDeparture $schoolDeparture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolDeparture->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($schoolDeparture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_departure_index', [], Response::HTTP_SEE_OTHER);
    }
}
