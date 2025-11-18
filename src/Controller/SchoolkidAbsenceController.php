<?php

namespace App\Controller;

use App\Entity\SchoolkidAbsence;
use App\Form\SchoolkidAbsenceType;
use App\Repository\SchoolkidAbsenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/schoolkid/absence')]
final class SchoolkidAbsenceController extends AbstractController
{
    #[Route(name: 'app_schoolkid_absence_index', methods: ['GET'])]
    public function index(SchoolkidAbsenceRepository $schoolkidAbsenceRepository): Response
    {
        return $this->render('schoolkid_absence/index.html.twig', [
            'schoolkid_absences' => $schoolkidAbsenceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_schoolkid_absence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schoolkidAbsence = new SchoolkidAbsence();
        $form = $this->createForm(SchoolkidAbsenceType::class, $schoolkidAbsence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($schoolkidAbsence);
            $entityManager->flush();

            return $this->redirectToRoute('app_schoolkid_absence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('schoolkid_absence/new.html.twig', [
            'schoolkid_absence' => $schoolkidAbsence,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_schoolkid_absence_show', methods: ['GET'])]
    public function show(SchoolkidAbsence $schoolkidAbsence): Response
    {
        return $this->render('schoolkid_absence/show.html.twig', [
            'schoolkid_absence' => $schoolkidAbsence,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_schoolkid_absence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SchoolkidAbsence $schoolkidAbsence, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SchoolkidAbsenceType::class, $schoolkidAbsence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_schoolkid_absence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('schoolkid_absence/edit.html.twig', [
            'schoolkid_absence' => $schoolkidAbsence,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_schoolkid_absence_delete', methods: ['POST'])]
    public function delete(Request $request, SchoolkidAbsence $schoolkidAbsence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolkidAbsence->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($schoolkidAbsence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_schoolkid_absence_index', [], Response::HTTP_SEE_OTHER);
    }
}
