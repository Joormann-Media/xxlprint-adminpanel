<?php

namespace App\Controller;

use App\Entity\ReadmeManager;
use App\Form\ReadmeManagerForm;
use App\Repository\ReadmeManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/readme/manager')]
final class ReadmeManagerController extends AbstractController
{
    #[Route(name: 'app_readme_manager_index', methods: ['GET'])]
    public function index(ReadmeManagerRepository $readmeManagerRepository): Response
    {
        return $this->render('readme_manager/index.html.twig', [
            'readme_managers' => $readmeManagerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_readme_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $readmeManager = new ReadmeManager();
        $form = $this->createForm(ReadmeManagerForm::class, $readmeManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($readmeManager);
            $entityManager->flush();

            return $this->redirectToRoute('app_readme_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('readme_manager/new.html.twig', [
            'readme_manager' => $readmeManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_readme_manager_show', methods: ['GET'])]
    public function show(ReadmeManager $readmeManager): Response
    {
        return $this->render('readme_manager/show.html.twig', [
            'readme_manager' => $readmeManager,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_readme_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReadmeManager $readmeManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReadmeManagerForm::class, $readmeManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_readme_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('readme_manager/edit.html.twig', [
            'readme_manager' => $readmeManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_readme_manager_delete', methods: ['POST'])]
    public function delete(Request $request, ReadmeManager $readmeManager, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$readmeManager->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($readmeManager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_readme_manager_index', [], Response::HTTP_SEE_OTHER);
    }
}
