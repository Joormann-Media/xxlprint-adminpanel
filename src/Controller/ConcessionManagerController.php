<?php

namespace App\Controller;

use App\Entity\ConcessionManager;
use App\Form\ConcessionManagerType;
use App\Repository\ConcessionManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/concession/manager')]
final class ConcessionManagerController extends AbstractController
{
    #[Route(name: 'app_concession_manager_index', methods: ['GET'])]
    public function index(ConcessionManagerRepository $concessionManagerRepository): Response
    {
        return $this->render('concession_manager/index.html.twig', [
            'concession_managers' => $concessionManagerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_concession_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $concessionManager = new ConcessionManager();
        $form = $this->createForm(ConcessionManagerType::class, $concessionManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($concessionManager);
            $entityManager->flush();

            return $this->redirectToRoute('app_concession_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('concession_manager/new.html.twig', [
            'concession_manager' => $concessionManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_concession_manager_show', methods: ['GET'])]
    public function show(ConcessionManager $concessionManager): Response
    {
        return $this->render('concession_manager/show.html.twig', [
            'concession_manager' => $concessionManager,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_concession_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ConcessionManager $concessionManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConcessionManagerType::class, $concessionManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_concession_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('concession_manager/edit.html.twig', [
            'concession_manager' => $concessionManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_concession_manager_delete', methods: ['POST'])]
    public function delete(Request $request, ConcessionManager $concessionManager, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$concessionManager->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($concessionManager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_concession_manager_index', [], Response::HTTP_SEE_OTHER);
    }
}
