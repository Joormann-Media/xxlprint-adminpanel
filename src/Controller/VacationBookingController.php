<?php

namespace App\Controller;

use App\Entity\VacationBooking;
use App\Form\VacationBookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/vacation/booking')]
final class VacationBookingController extends AbstractController
{
    #[Route(name: 'app_vacation_booking_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $vacationBookings = $entityManager
            ->getRepository(VacationBooking::class)
            ->findAll();

        return $this->render('vacation_booking/index.html.twig', [
            'vacation_bookings' => $vacationBookings,
        ]);
    }

    #[Route('/new', name: 'app_vacation_booking_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vacationBooking = new VacationBooking();
        $form = $this->createForm(VacationBookingType::class, $vacationBooking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vacationBooking);
            $entityManager->flush();

            return $this->redirectToRoute('app_vacation_booking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vacation_booking/new.html.twig', [
            'vacation_booking' => $vacationBooking,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_vacation_booking_show', methods: ['GET'])]
    public function show(VacationBooking $vacationBooking): Response
    {
        return $this->render('vacation_booking/show.html.twig', [
            'vacation_booking' => $vacationBooking,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_vacation_booking_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VacationBooking $vacationBooking, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VacationBookingType::class, $vacationBooking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vacation_booking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vacation_booking/edit.html.twig', [
            'vacation_booking' => $vacationBooking,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_vacation_booking_delete', methods: ['POST'])]
    public function delete(Request $request, VacationBooking $vacationBooking, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vacationBooking->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($vacationBooking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vacation_booking_index', [], Response::HTTP_SEE_OTHER);
    }
}
