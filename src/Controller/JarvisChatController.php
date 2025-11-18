<?php

namespace App\Controller;

use App\Entity\JarvisChatLog;
use App\Repository\JarvisChatLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jarvis')]
class JarvisChatController extends AbstractController
{
    #[Route('', name: 'jarvis_chat', methods: ['GET'])]
    public function chat(JarvisChatLogRepository $repo): Response
    {
        // Letzten 20 Chats holen, neuste zuerst
        $logs = $repo->findBy([], ['createdAt' => 'DESC'], 20);

        return $this->render('jarvis/chat.html.twig', [
            'chat_logs' => $logs,
            'page_title' => 'Jarvis Chat',
            'page_description' => 'Interagiere mit Jarvis, unserem KI-gestützten Chatbot.',
            'page_keywords' => 'Jarvis, Chatbot, KI, Mistral',
        ]);
    }

    #[Route('/prompt', name: 'jarvis_prompt', methods: ['POST'])]
    public function prompt(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $prompt = trim($request->request->get('prompt', ''));
        if (!$prompt) {
            return new JsonResponse(['error' => 'Prompt darf nicht leer sein.'], 400);
        }

        // Anbindung an Jarvis (Ollama/Mistral)
        $response = $this->askJarvis($prompt);

        // Loggen
        $chat = new JarvisChatLog();
        $chat->setUser($this->getUser());
        $chat->setPrompt($prompt);
        $chat->setResponse($response['text'] ?? '');
        $chat->setStatus($response['status'] ?? 'done');
        $chat->setCreatedAt(new \DateTimeImmutable());
        $em->persist($chat);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'id' => $chat->getId(),
            'prompt' => $prompt,
            'response' => $chat->getResponse(),
            'status' => $chat->getStatus(),
            'createdAt' => $chat->getCreatedAt()->format('d.m.Y H:i:s')
        ]);
    }

    private function askJarvis(string $prompt): array
    {
        $ollamaUrl = 'http://localhost:11434/api/generate';
        $payload = [
            'model' => 'mistral',  // Modellname ggf. anpassen
            'prompt' => $prompt,
            'stream' => false
        ];

        $ch = curl_init($ollamaUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $result = curl_exec($ch);
        curl_close($ch);

        $data = @json_decode($result, true);
        return [
            'text' => $data['response'] ?? '[Fehler: Keine Antwort von Jarvis]',
            'status' => isset($data['response']) ? 'done' : 'error',
        ];
    }
}
