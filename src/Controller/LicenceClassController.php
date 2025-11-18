<?php

namespace App\Controller;

use App\Entity\LicenceClass;
use App\Form\LicenceClassType;
use App\Repository\LicenceClassRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/licence/class')]
final class LicenceClassController extends AbstractController
{
    #[Route(name: 'app_licence_class_index', methods: ['GET'])]
    public function index(LicenceClassRepository $licenceClassRepository): Response
    {
        return $this->render('licence_class/index.html.twig', [
            'licence_classes' => $licenceClassRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_licence_class_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $licenceClass = new LicenceClass();
        $form = $this->createForm(LicenceClassType::class, $licenceClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($licenceClass);
            $entityManager->flush();

            return $this->redirectToRoute('app_licence_class_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('licence_class/new.html.twig', [
            'licence_class' => $licenceClass,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_licence_class_show', methods: ['GET'])]
    public function show(LicenceClass $licenceClass): Response
    {
        return $this->render('licence_class/show.html.twig', [
            'licence_class' => $licenceClass,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_licence_class_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LicenceClass $licenceClass, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LicenceClassType::class, $licenceClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_licence_class_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('licence_class/edit.html.twig', [
            'licence_class' => $licenceClass,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_licence_class_delete', methods: ['POST'])]
    public function delete(Request $request, LicenceClass $licenceClass, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$licenceClass->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($licenceClass);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_licence_class_index', [], Response::HTTP_SEE_OTHER);
    }
}
