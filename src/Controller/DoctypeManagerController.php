<?php

namespace App\Controller;

use App\Entity\DoctypeManager;
use App\Form\DoctypeManagerType;
use App\Repository\DoctypeManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/doctype/manager')]
final class DoctypeManagerController extends AbstractController
{
    #[Route(name: 'app_doctype_manager_index', methods: ['GET'])]
    public function index(DoctypeManagerRepository $doctypeManagerRepository): Response
    {
        return $this->render('doctype_manager/index.html.twig', [
            'doctype_managers' => $doctypeManagerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_doctype_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $doctypeManager = new DoctypeManager();
        $form = $this->createForm(DoctypeManagerType::class, $doctypeManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($doctypeManager);
            $entityManager->flush();

            return $this->redirectToRoute('app_doctype_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('doctype_manager/new.html.twig', [
            'doctype_manager' => $doctypeManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_doctype_manager_show', methods: ['GET'])]
    public function show(DoctypeManager $doctypeManager): Response
    {
        return $this->render('doctype_manager/show.html.twig', [
            'doctype_manager' => $doctypeManager,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_doctype_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DoctypeManager $doctypeManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DoctypeManagerType::class, $doctypeManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_doctype_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('doctype_manager/edit.html.twig', [
            'doctype_manager' => $doctypeManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_doctype_manager_delete', methods: ['POST'])]
    public function delete(Request $request, DoctypeManager $doctypeManager, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$doctypeManager->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($doctypeManager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_doctype_manager_index', [], Response::HTTP_SEE_OTHER);
    }
}
