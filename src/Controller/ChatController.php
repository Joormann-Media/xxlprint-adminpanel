<?php
namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/chat/send', name: 'chat_send', methods: ['POST'])]
    public function sendMessage(
        Request $request,
        EntityManagerInterface $em,
        ConversationRepository $convRepo,
        UserRepository $userRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['recipientId'], $data['content'])) {
            return $this->json(['error' => 'Ungültige Daten'], 400);
        }

        $sender = $this->getUser();
        $recipient = $userRepo->find($data['recipientId']);

        if (!$recipient) {
            return $this->json(['error' => 'Empfänger nicht gefunden'], 404);
        }

        $conversation = $convRepo->findOneBetweenTwoUsers($sender, $recipient);
        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->addParticipant($sender);
            $conversation->addParticipant($recipient);
            $em->persist($conversation);
        }

        $message = new Message();
        $message->setSender($sender)
                ->setRecipient($recipient)
                ->setContent($data['content'])
                ->setConversation($conversation);

        $conversation->setLastMessageAt($message->getSentAt());

        $em->persist($message);
        $em->flush();

        // Push über WebSocket an Server
        $msgArray = [
            'type' => 'new_message',
            'conversationId' => $conversation->getId(),
            'sender' => $sender->getUsername(),
            'senderId' => $sender->getId(),
            'recipient' => $recipient->getUsername(),
            'recipientId' => $recipient->getId(),
            'content' => $message->getContent(),
            'timestamp' => $message->getSentAt()->format('c'),
        ];

        $fp = @stream_socket_client("tcp://127.0.0.1:8075", $errno, $errstr, 1);
        if ($fp) {
            fwrite($fp, json_encode($msgArray));
            fclose($fp);
        }

        return $this->json(['status' => 'sent']);
    }

    #[Route('/chat/messages/{userId}', name: 'chat_messages')]
    public function getMessages(
        int $userId,
        ConversationRepository $convRepo,
        UserRepository $userRepo
    ): JsonResponse {
        $me = $this->getUser();
        $other = $userRepo->find($userId);

        if (!$other) {
            return $this->json([]);
        }

        $conversation = $convRepo->findOneBetweenTwoUsers($me, $other);

        if (!$conversation) {
            return $this->json([]);
        }

        $messages = $conversation->getMessages();

        $output = [];
        foreach ($messages as $msg) {
            $output[] = [
                'from' => $msg->getSender()->getUsername(),
                'to' => $msg->getRecipient()->getUsername(),
                'content' => $msg->getContent(),
                'timestamp' => $msg->getSentAt()->format('Y-m-d H:i'),
            ];
        }

        return $this->json($output);
    }
}
