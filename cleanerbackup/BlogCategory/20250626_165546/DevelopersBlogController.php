<?php

namespace App\Controller;

use App\Entity\DevelopersBlog;
use App\Form\DevelopersBlogForm;
use App\Repository\DevelopersBlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/developers/blog')]
final class DevelopersBlogController extends AbstractController
{
    #[Route(name: 'app_developers_blog_index', methods: ['GET'])]
    public function index(DevelopersBlogRepository $developersBlogRepository): Response
    {
        return $this->render('developers_blog/index.html.twig', [
            'developers_blogs' => $developersBlogRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_developers_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $developersBlog = new DevelopersBlog();
        $form = $this->createForm(DevelopersBlogForm::class, $developersBlog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($developersBlog);
            $entityManager->flush();

            return $this->redirectToRoute('app_developers_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('developers_blog/new.html.twig', [
            'developers_blog' => $developersBlog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_developers_blog_show', methods: ['GET'])]
    public function show(DevelopersBlog $developersBlog): Response
    {
        return $this->render('developers_blog/show.html.twig', [
            'developers_blog' => $developersBlog,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_developers_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DevelopersBlog $developersBlog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevelopersBlogForm::class, $developersBlog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_developers_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('developers_blog/edit.html.twig', [
            'developers_blog' => $developersBlog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_developers_blog_delete', methods: ['POST'])]
    public function delete(Request $request, DevelopersBlog $developersBlog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$developersBlog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($developersBlog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_developers_blog_index', [], Response::HTTP_SEE_OTHER);
    }
}
