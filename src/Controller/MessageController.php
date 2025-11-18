<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\MessageRecipient;
use App\Entity\MessageStatus;
use App\Entity\User;
use App\Entity\UserGroups;
use App\Entity\Attachment;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\MessageStatusRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/message')]
final class MessageController extends AbstractController
{
    #[Route(name: 'app_message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Setze automatisch den Ersteller und das Datum
            $message->setSender($this->getUser());
            $message->setCreatedAt(new \DateTime());

            // ----- Empfänger-Handling -----
            $selectedUsers = $form->get('recipients')->getData(); // User[]
            $selectedGroups = $form->get('groups')->getData();    // UserGroups[]

            // 1. Einzelne User als Empfänger (+ Tracker)
            foreach ($selectedUsers as $user) {
                $recipient = new MessageRecipient();
                $recipient->setMessage($message);
                $recipient->setRecipientUser($user);

                // Tracker anlegen und deliveredAt setzen
                $status = new MessageStatus();
                $status->setRecipient($recipient);
                $status->setDeliveredAt(new \DateTime());
                $entityManager->persist($status);

                $entityManager->persist($recipient);
                $message->addRecipient($recipient);
            }

            // 2. Gruppen als Empfänger (+ Tracker)
            foreach ($selectedGroups as $group) {
                $recipient = new MessageRecipient();
                $recipient->setMessage($message);
                $recipient->setRecipientGroup($group);

                // Tracker anlegen und deliveredAt setzen
                $status = new MessageStatus();
                $status->setRecipient($recipient);
                $status->setDeliveredAt(new \DateTime());
                $entityManager->persist($status);

                $entityManager->persist($recipient);
                $message->addRecipient($recipient);
            }

            // 3. Attachments (optional, wenn verwendet)
            /** @var UploadedFile[] $files */
            $files = $form->get('attachments')->getData();
            foreach ($files as $file) {
                $filename = uniqid().'_'.$file->getClientOriginalName();
                // Zielpfad anpassen!
                $targetDir = $this->getParameter('attachments_directory') ?? 'public/uploads/attachments';
                $file->move($targetDir, $filename);

                $attachment = new Attachment();
                $attachment->setMessage($message);
                $attachment->setFilename($file->getClientOriginalName());
                $attachment->setFilepath($targetDir . '/' . $filename);
                $attachment->setMimetype($file->getMimeType());
                $attachment->setUploadedAt(new \DateTime());
                $entityManager->persist($attachment);
                $message->addAttachment($attachment);
            }

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_message_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hinweis: Bei Edit ggf. Empfänger/Attachments aktualisieren, sonst wie gehabt
            $entityManager->flush();

            return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_message_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/inbox', name: 'app_message_inbox', methods: ['GET'])]
public function inbox(MessageStatusRepository $messageStatusRepository): Response
{
    $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException('Login erforderlich');
    }

    // Nur jeweils letzte Nachricht pro Absender
    $latestPerSender = $messageStatusRepository->findLastMessagesPerSenderForUser($user);

    return $this->render('message/inbox.html.twig', [
        'latestMessages' => $latestPerSender,
    ]);
}

#[Route('/modal/{id<\d+>}', name: 'app_message_modal', methods: ['GET'])]
public function modal(Message $message): Response
{
    return $this->render('message/_modal_content.html.twig', [
        'message' => $message,
    ]);
}

#[Route('/chat/{senderId}', name: 'app_chat_by_sender', methods: ['GET'])]
public function chatBySender(
    int $senderId,
    MessageStatusRepository $messageStatusRepository
): Response {
    $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException();
    }

    $messages = $messageStatusRepository->findAllMessagesForUser($user);

    $chat = array_filter($messages, function ($status) use ($senderId) {
        return $status->getRecipient()->getMessage()->getSender()->getId() === $senderId;
    });

    usort($chat, function ($a, $b) {
        return $a->getRecipient()->getMessage()->getCreatedAt() <=> $b->getRecipient()->getMessage()->getCreatedAt();
    });

    return $this->render('message/_chat_view.html.twig', [
        'chat' => $chat,
    ]);
}
#[Route('/unread-count', name: 'app_message_unread_count', methods: ['GET'])]
public function getUnreadCount(MessageStatusRepository $repo): JsonResponse
{
    $user = $this->getUser();
      
    if (!$user) {
        return new JsonResponse(['unreadMessages' => 0, 'urgentNews' => 0]);
    }

    $unread = $repo->countUnreadMessagesForUser($user);
    $urgent = $repo->countUrgentUnreadMessagesForUser($user);

    return new JsonResponse([
        'unreadMessages' => $unread,
        'urgentNews' => $urgent
    ]);
}



}
