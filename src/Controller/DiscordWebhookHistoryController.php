<?php

namespace App\Controller;

use App\Entity\DiscordWebhookHistory;
use App\Form\DiscordWebhookHistoryForm;
use App\Repository\DiscordWebhookHistoryRepository;
use App\Service\DiscordWebhookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/discord/webhook/history')]
final class DiscordWebhookHistoryController extends AbstractController
{
    private DiscordWebhookService $webhookService;
    private EntityManagerInterface $entityManager;

    public function __construct(DiscordWebhookService $webhookService, EntityManagerInterface $entityManager)
    {
        $this->webhookService = $webhookService;
        $this->entityManager = $entityManager;
    }

    #[Route(name: 'app_discord_webhook_history_index', methods: ['GET'])]
    public function index(DiscordWebhookHistoryRepository $discordWebhookHistoryRepository): Response
    {
        return $this->render('discord_webhook_history/index.html.twig', [
            'discord_webhook_histories' => $discordWebhookHistoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_discord_webhook_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $discordWebhookHistory = new DiscordWebhookHistory();
        $form = $this->createForm(DiscordWebhookHistoryForm::class, $discordWebhookHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Webhook schießen und Status setzen
            $ok = $this->webhookService->send($discordWebhookHistory->getHooktext());
            $discordWebhookHistory->setHookstatus($ok ? 'success' : 'error');

            // Optional: Aktuellen User als Author setzen
            if (method_exists($discordWebhookHistory, 'setUsername') && $this->getUser()) {
                $discordWebhookHistory->setUsername($this->getUser());
            }

            $this->entityManager->persist($discordWebhookHistory);
            $this->entityManager->flush();

            $this->addFlash($ok ? 'success' : 'danger', $ok ? 'Webhook gesendet!' : 'Fehler beim Senden an Discord!');

            return $this->redirectToRoute('app_discord_webhook_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('discord_webhook_history/new.html.twig', [
            'discord_webhook_history' => $discordWebhookHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_discord_webhook_history_show', methods: ['GET'])]
    public function show(DiscordWebhookHistory $discordWebhookHistory): Response
    {
        return $this->render('discord_webhook_history/show.html.twig', [
            'discord_webhook_history' => $discordWebhookHistory,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_discord_webhook_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DiscordWebhookHistory $discordWebhookHistory): Response
    {
        $form = $this->createForm(DiscordWebhookHistoryForm::class, $discordWebhookHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Webhook schießen und Status setzen
            $ok = $this->webhookService->send($discordWebhookHistory->getHooktext());
            $discordWebhookHistory->setHookstatus($ok ? 'success' : 'error');

            // Optional: User updaten (bei Edit selten nötig)
            if (method_exists($discordWebhookHistory, 'setUsername') && $this->getUser()) {
                $discordWebhookHistory->setUsername($this->getUser());
            }

            $this->entityManager->flush();

            $this->addFlash($ok ? 'success' : 'danger', $ok ? 'Webhook gesendet (Update)!' : 'Fehler beim Senden!');

            return $this->redirectToRoute('app_discord_webhook_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('discord_webhook_history/edit.html.twig', [
            'discord_webhook_history' => $discordWebhookHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_discord_webhook_history_delete', methods: ['POST'])]
    public function delete(Request $request, DiscordWebhookHistory $discordWebhookHistory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$discordWebhookHistory->getId(), $request->getPayload()->getString('_token'))) {
            $this->entityManager->remove($discordWebhookHistory);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_discord_webhook_history_index', [], Response::HTTP_SEE_OTHER);
    }
}
