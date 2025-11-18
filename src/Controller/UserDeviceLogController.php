<?php

namespace App\Controller;

use App\Entity\UserDeviceLog;
use App\Form\UserDeviceLogForm;
use App\Repository\UserDeviceLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/device/log')]
final class UserDeviceLogController extends AbstractController
{
    #[Route(name: 'app_user_device_log_index', methods: ['GET'])]
    public function index(UserDeviceLogRepository $userDeviceLogRepository): Response
    {
        return $this->render('user_device_log/index.html.twig', [
            'user_device_logs' => $userDeviceLogRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_device_log_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userDeviceLog = new UserDeviceLog();
        $form = $this->createForm(UserDeviceLogForm::class, $userDeviceLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userDeviceLog);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_device_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_device_log/new.html.twig', [
            'user_device_log' => $userDeviceLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_device_log_show', methods: ['GET'])]
    public function show(UserDeviceLog $userDeviceLog): Response
    {
        return $this->render('user_device_log/show.html.twig', [
            'user_device_log' => $userDeviceLog,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_device_log_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserDeviceLog $userDeviceLog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserDeviceLogForm::class, $userDeviceLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_device_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_device_log/edit.html.twig', [
            'user_device_log' => $userDeviceLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_device_log_delete', methods: ['POST'])]
    public function delete(Request $request, UserDeviceLog $userDeviceLog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userDeviceLog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userDeviceLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_device_log_index', [], Response::HTTP_SEE_OTHER);
    }
}
