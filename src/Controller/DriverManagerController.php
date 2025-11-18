<?php

namespace App\Controller;

use App\Entity\DriverManager;
use App\Form\DriverManagerType;
use App\Repository\DriverManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/driver/manager')]
final class DriverManagerController extends AbstractController
{
    #[Route(name: 'app_driver_manager_index', methods: ['GET'])]
    public function index(DriverManagerRepository $driverManagerRepository): Response
    {
        return $this->render('driver_manager/index.html.twig', [
            'driver_managers' => $driverManagerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_driver_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $driverManager = new DriverManager();
        $form = $this->createForm(DriverManagerType::class, $driverManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($driverManager);
            $entityManager->flush();

            return $this->redirectToRoute('app_driver_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('driver_manager/new.html.twig', [
            'driver_manager' => $driverManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_driver_manager_show', methods: ['GET'])]
    public function show(DriverManager $driverManager): Response
    {
        return $this->render('driver_manager/show.html.twig', [
            'driver_manager' => $driverManager,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_driver_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DriverManager $driverManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DriverManagerType::class, $driverManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_driver_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('driver_manager/edit.html.twig', [
            'driver_manager' => $driverManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_driver_manager_delete', methods: ['POST'])]
    public function delete(Request $request, DriverManager $driverManager, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$driverManager->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($driverManager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_driver_manager_index', [], Response::HTTP_SEE_OTHER);
    }
}
