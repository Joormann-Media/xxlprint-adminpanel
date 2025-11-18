<?php

namespace App\Controller\Api;

use App\Repository\UserLoginHistoryRepository;
use App\Repository\UserSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/api/user', name: 'api_user_')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class UserSessionApiController extends AbstractController
{
    #[Route('/logins', name: 'login_history', methods: ['GET'])]
    public function loginHistory(UserLoginHistoryRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        $entries = $repo->findBy(['user' => $user], ['loginAt' => 'DESC'], 20);

        $data = array_map(fn($entry) => [
            'loginAt' => $entry->getLoginAt()?->format('Y-m-d H:i:s'),
            'ipAddress' => $entry->getIpAddress(),
            'userAgent' => $entry->getUserAgent(),
        ], $entries);

        return $this->json($data);
    }

    #[Route('/sessions', name: 'sessions', methods: ['GET'])]
    public function sessions(UserSessionRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        $sessions = $repo->findBy(['user' => $user], ['createdAt' => 'DESC']);

        $data = array_map(fn($session) => [
            'id' => $session->getId(),
            'sessionId' => $session->getSessionId(),
            'createdAt' => $session->getCreatedAt()?->format('Y-m-d H:i:s'),
            'ipAddress' => $session->getIp(),
            'userAgent' => $session->getUserAgent(),
            'isActive' => $session->isActive(),
        ], $sessions);

        return $this->json($data);
    }
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $user = $this->getUser();
        $sessions = $this->repo->findActiveByUser($user->getId());

        $data = array_map(fn($session) => [
            'id' => $session->getId(),
            'sessionId' => $session->getSessionId(),
            'ip' => $session->getIp(),
            'userAgent' => $session->getUserAgent(),
            'createdAt' => $session->getCreatedAt()->format('Y-m-d H:i:s'),
            'lastActiveAt' => $session->getLastActiveAt()?->format('Y-m-d H:i:s'),
            'isActive' => $session->isActive(),
        ], $sessions);

        return $this->json($data);
    }

    #[Route('/api/me/sessions', name: 'api_me_sessions', methods: ['GET'])]
public function listMySessions(UserSessionRepository $repo, Security $security): JsonResponse
{
    $user = $security->getUser();

    if (!$user || !method_exists($user, 'getId')) {
        return $this->json(['error' => 'Unauthorized'], 401);
    }

    $sessions = $repo->findBy(['user' => $user], ['lastActiveAt' => 'DESC']);
    
    return $this->json(array_map(fn(UserSession $s) => [
        'session_id' => $s->getSessionId(),
        'created_at' => $s->getCreatedAt()?->format(\DateTime::ATOM),
        'last_active_at' => $s->getLastActiveAt()?->format(\DateTime::ATOM),
        'ip' => $s->getIp(),
        'user_agent' => $s->getUserAgent(),
        'is_active' => $s->getIsActive(),
        'trusted' => $s->getIsTrusted(),
    ], $sessions));
}

    
}
