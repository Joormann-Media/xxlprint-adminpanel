<?php

namespace App\Controller;

use App\Entity\EmployeeVacation;
use App\Form\EmployeeVacationType;
use App\Repository\EmployeeVacationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employee/vacation')]
final class EmployeeVacationController extends AbstractController
{
    #[Route(name: 'app_employee_vacation_index', methods: ['GET'])]
    public function index(EmployeeVacationRepository $employeeVacationRepository): Response
    {
        return $this->render('employee_vacation/index.html.twig', [
            'employee_vacations' => $employeeVacationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_employee_vacation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employeeVacation = new EmployeeVacation();
        $form = $this->createForm(EmployeeVacationType::class, $employeeVacation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employeeVacation);
            $entityManager->flush();

            return $this->redirectToRoute('app_employee_vacation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee_vacation/new.html.twig', [
            'employee_vacation' => $employeeVacation,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_employee_vacation_show', methods: ['GET'])]
    public function show(EmployeeVacation $employeeVacation): Response
    {
        return $this->render('employee_vacation/show.html.twig', [
            'employee_vacation' => $employeeVacation,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_employee_vacation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EmployeeVacation $employeeVacation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeeVacationType::class, $employeeVacation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_employee_vacation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee_vacation/edit.html.twig', [
            'employee_vacation' => $employeeVacation,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_employee_vacation_delete', methods: ['POST'])]
    public function delete(Request $request, EmployeeVacation $employeeVacation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employeeVacation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($employeeVacation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_employee_vacation_index', [], Response::HTTP_SEE_OTHER);
    }
}
