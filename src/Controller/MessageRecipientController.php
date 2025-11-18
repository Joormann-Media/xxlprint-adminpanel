<?php

namespace App\Controller;

use App\Entity\MessageRecipient;
use App\Form\MessageRecipientType;
use App\Repository\MessageRecipientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/message/recipient')]
final class MessageRecipientController extends AbstractController
{
    #[Route(name: 'app_message_recipient_index', methods: ['GET'])]
    public function index(MessageRecipientRepository $messageRecipientRepository): Response
    {
        return $this->render('message_recipient/index.html.twig', [
            'message_recipients' => $messageRecipientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_message_recipient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $messageRecipient = new MessageRecipient();
        $form = $this->createForm(MessageRecipientType::class, $messageRecipient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($messageRecipient);
            $entityManager->flush();

            return $this->redirectToRoute('app_message_recipient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message_recipient/new.html.twig', [
            'message_recipient' => $messageRecipient,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_message_recipient_show', methods: ['GET'])]
    public function show(MessageRecipient $messageRecipient): Response
    {
        return $this->render('message_recipient/show.html.twig', [
            'message_recipient' => $messageRecipient,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_message_recipient_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MessageRecipient $messageRecipient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessageRecipientType::class, $messageRecipient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_message_recipient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message_recipient/edit.html.twig', [
            'message_recipient' => $messageRecipient,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_message_recipient_delete', methods: ['POST'])]
    public function delete(Request $request, MessageRecipient $messageRecipient, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$messageRecipient->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($messageRecipient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_message_recipient_index', [], Response::HTTP_SEE_OTHER);
    }
}
