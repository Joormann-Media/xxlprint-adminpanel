<?php

namespace App\Controller;

use App\Entity\SchoolTime;
use App\Form\SchoolTimeType;
use App\Repository\SchoolTimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/school-time')]
final class SchoolTimeController extends AbstractController
{
    #[Route(name: 'app_school_time_index', methods: ['GET'])]
    public function index(SchoolTimeRepository $schoolTimeRepository): Response
    {
        return $this->render('school_time/index.html.twig', [
            'school_times' => $schoolTimeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_school_time_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schoolTime = new SchoolTime();
        $form = $this->createForm(SchoolTimeType::class, $schoolTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($schoolTime);
            $entityManager->flush();

            return $this->redirectToRoute('app_school_time_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('school_time/new.html.twig', [
            'school_time' => $schoolTime,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_school_time_show', methods: ['GET'])]
    public function show(SchoolTime $schoolTime): Response
    {
        return $this->render('school_time/show.html.twig', [
            'school_time' => $schoolTime,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_school_time_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SchoolTime $schoolTime, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SchoolTimeType::class, $schoolTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_school_time_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('school_time/edit.html.twig', [
            'school_time' => $schoolTime,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_school_time_delete', methods: ['POST'])]
    public function delete(Request $request, SchoolTime $schoolTime, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolTime->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($schoolTime);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_time_index', [], Response::HTTP_SEE_OTHER);
    }
}
