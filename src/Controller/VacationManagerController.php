<?php

namespace App\Controller;

use App\Entity\VacationManager;
use App\Form\VacationManagerType;
use App\Repository\VacationManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/vacation-manager')]
final class VacationManagerController extends AbstractController
{
    #[Route(name: 'app_vacation_manager_index', methods: ['GET'])]
    public function index(VacationManagerRepository $vacationManagerRepository): Response
    {
        return $this->render('vacation_manager/index.html.twig', [
            'vacation_managers' => $vacationManagerRepository->findAll(),
            'page_title' => 'Urlaubs-Manager - Übersicht',
        ]);
    }

    #[Route('/new', name: 'app_vacation_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vacationManager = new VacationManager();
        $form = $this->createForm(VacationManagerType::class, $vacationManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vacationManager);
            $entityManager->flush();

            return $this->redirectToRoute('app_vacation_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vacation_manager/new.html.twig', [
            'vacation_manager' => $vacationManager,
            'form' => $form,
            'page_title' => 'Urlaubs-Manager - Neuer Eintrag',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_vacation_manager_show', methods: ['GET'])]
    public function show(VacationManager $vacationManager): Response
    {
        return $this->render('vacation_manager/show.html.twig', [
            'vacation_manager' => $vacationManager,
            'page_title' => 'Urlaubs-Manager - Details',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_vacation_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VacationManager $vacationManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VacationManagerType::class, $vacationManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vacation_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vacation_manager/edit.html.twig', [
            'vacation_manager' => $vacationManager,
            'form' => $form,
            'page_title' => 'Urlaubs-Manager - Eintrag bearbeiten',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_vacation_manager_delete', methods: ['POST'])]
    public function delete(Request $request, VacationManager $vacationManager, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vacationManager->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vacationManager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vacation_manager_index', [], Response::HTTP_SEE_OTHER);
    }
}
