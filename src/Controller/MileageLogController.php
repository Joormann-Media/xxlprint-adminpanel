<?php

namespace App\Controller;

use App\Entity\MileageLog;
use App\Form\MileageLogType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mileage/log')]
final class MileageLogController extends AbstractController
{
    #[Route(name: 'app_mileage_log_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $mileageLogs = $entityManager
            ->getRepository(MileageLog::class)
            ->findAll();

        return $this->render('mileage_log/index.html.twig', [
            'mileage_logs' => $mileageLogs,
        ]);
    }

    #[Route('/new', name: 'app_mileage_log_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mileageLog = new MileageLog();
        $form = $this->createForm(MileageLogType::class, $mileageLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mileageLog);
            $entityManager->flush();

            return $this->redirectToRoute('app_mileage_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mileage_log/new.html.twig', [
            'mileage_log' => $mileageLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_mileage_log_show', methods: ['GET'])]
    public function show(MileageLog $mileageLog): Response
    {
        return $this->render('mileage_log/show.html.twig', [
            'mileage_log' => $mileageLog,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_mileage_log_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MileageLog $mileageLog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MileageLogType::class, $mileageLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_mileage_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mileage_log/edit.html.twig', [
            'mileage_log' => $mileageLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_mileage_log_delete', methods: ['POST'])]
    public function delete(Request $request, MileageLog $mileageLog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mileageLog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($mileageLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mileage_log_index', [], Response::HTTP_SEE_OTHER);
    }
}
