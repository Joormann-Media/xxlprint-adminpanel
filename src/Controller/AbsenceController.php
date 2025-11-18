<?php

// src/Controller/AbsenceController.php
namespace App\Controller;

use App\Entity\SchoolkidAbsence;
use App\Entity\EmployeeAbsence;
use App\Form\SchoolkidAbsenceType;
use App\Form\EmployeeAbsenceType;
use App\Repository\SchoolkidsRepository;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbsenceController extends AbstractController
{
    #[Route('/abmeldung', name: 'app_absence_combined')]
    public function combined(
        Request $request,
        EntityManagerInterface $em,
        SchoolkidsRepository $schoolkidRepo,
        EmployeeRepository $employeeRepo
    ): Response {
        // Beide Formulare vorbereiten
        $schoolkidAbsence = new SchoolkidAbsence();
        $schoolkidForm = $this->createForm(SchoolkidAbsenceType::class, $schoolkidAbsence, [
            'action' => $this->generateUrl('app_absence_combined'),
            'method' => 'POST',
            'attr'   => ['id' => 'form_schoolkid'],
        ]);
        $schoolkidForm->handleRequest($request);

        $employeeAbsence = new EmployeeAbsence();
        $employeeForm = $this->createForm(EmployeeAbsenceType::class, $employeeAbsence, [
            'action' => $this->generateUrl('app_absence_combined'),
            'method' => 'POST',
            'attr'   => ['id' => 'form_employee'],
        ]);
        $employeeForm->handleRequest($request);

        $submitted = false;
        $type = null;

        // Prüfen, welches Formular abgeschickt wurde
        if ($schoolkidForm->isSubmitted() && $schoolkidForm->isValid()) {
            $em->persist($schoolkidAbsence);
            $em->flush();
            $submitted = true;
            $type = 'schoolkid';
        } elseif ($employeeForm->isSubmitted() && $employeeForm->isValid()) {
            $em->persist($employeeAbsence);
            $em->flush();
            $submitted = true;
            $type = 'employee';
        }

        return $this->render('absence/combined.html.twig', [
            'schoolkid_form' => $schoolkidForm->createView(),
            'employee_form' => $employeeForm->createView(),
            'submitted' => $submitted,
            'type' => $type,
            'page_title' => 'Abmeldungen',
        ]);
    }
}

