<?php

namespace App\Controller;

use App\Entity\Documentations;
use App\Form\DocumentationsForm;
use App\Repository\DocumentationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/documentations')]
final class DocumentationsController extends AbstractController
{
    #[Route(name: 'app_documentations_index', methods: ['GET'])]
    public function index(DocumentationsRepository $documentationsRepository): Response
    {
        return $this->render('documentations/index.html.twig', [
            'documentations' => $documentationsRepository->findAll(),
            'documentationsCount' => $documentationsRepository->count([]),
            'page_title' => 'Documentations',
            'page_description' => 'Manage your documentations here.',
        ]);
    }

    #[Route('/new', name: 'app_documentations_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $documentation = new Documentations();
        $documentation->setDocuCreate(new \DateTimeImmutable());
        $form = $this->createForm(DocumentationsForm::class, $documentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $documentation->getDocuCreate()) {
                $documentation->setDocuCreate(new \DateTimeImmutable());
            }
            $entityManager->persist($documentation);
            $entityManager->flush();

            return $this->redirectToRoute('app_documentations_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('documentations/new.html.twig', [
            'documentation' => $documentation,
            'form' => $form,
            'page_title' => 'New Documentation',
            'page_description' => 'Create a new documentation entry.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_documentations_show', methods: ['GET'])]
    public function show(Documentations $documentation): Response
    {
        return $this->render('documentations/show.html.twig', [
            'documentation' => $documentation,
            'page_title' => $documentation->getDocuName(),
            'page_description' => $documentation->getDocuShortdescr() ?: 'No description available.',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_documentations_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Documentations $documentation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DocumentationsForm::class, $documentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_documentations_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('documentations/edit.html.twig', [
            'documentation' => $documentation,
            'form' => $form,
            'page_title' => 'Edit Documentation',
            'page_description' => 'Modify the documentation details.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_documentations_delete', methods: ['POST'])]
    public function delete(Request $request, Documentations $documentation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$documentation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($documentation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_documentations_index', [], Response::HTTP_SEE_OTHER);
    }
}
