<?php

namespace App\Controller\Api;

use App\Entity\UserLoginHistory;
use App\Repository\UserLoginHistoryRepository;
use App\Repository\UserRepository;
use App\Repository\UserSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class UserLoginApiController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em,
    ) {}

    // 🔐 Login
    #[Route('/login', name: 'login', methods: ['POST'])]
public function login(Request $request, UserLoginHistoryRepository $historyRepo): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $identifier     = $data['identifier']     ?? null;
    $password       = $data['password']       ?? null;
    $deviceId       = $data['device_id']      ?? null;
    $deviceType     = $data['device_type']    ?? 'unknown';
    $deviceName     = $data['device_name']    ?? 'Unbekanntes Gerät';

    if (!$identifier || !$password || !$deviceId) {
        return $this->json(['error' => 'Benutzerkennung, Passwort und Geräte-ID erforderlich.'], 400);
    }

    // 🔍 Benutzer identifizieren
    $user = $this->userRepository->findOneBy(['email' => $identifier])
        ?? $this->userRepository->findOneBy(['username' => $identifier])
        ?? $this->userRepository->findOneBy(['mobile' => $identifier])
        ?? $this->userRepository->findOneBy(['customerId' => $identifier]);

    $success = false;

    if ($user && $this->passwordHasher->isPasswordValid($user, $password)) {
        $success = true;
        $user->setLastlogindate(new \DateTime());
        $this->em->persist($user);
    }

    // 📝 Login-Historie immer erfassen
    $login = new UserLoginHistory();
    $login->setUser($user);
    $login->setIpAddress($request->getClientIp());
    $login->setUserAgent($request->headers->get('User-Agent'));
    $login->setLoginAt(new \DateTime());
    $login->setSuccess($success);
    $this->em->persist($login);

    if (!$success) {
        $this->em->flush();
        return $this->json(['error' => 'Login fehlgeschlagen.'], 401);
    }

    // 📱 Gerät anhand Fingerprint prüfen
    $device = $this->em->getRepository(\App\Entity\UserDevice::class)
        ->findOneBy(['user' => $user, 'deviceFingerprint' => $deviceId]);

    if ($device && !$device->isActive()) {
        return $this->json(['error' => 'Dieses Gerät wurde deaktiviert. Bitte Support kontaktieren.'], 403);
    }

    if (!$device) {
        $device = new \App\Entity\UserDevice();
        $device->setUser($user)
            ->setDeviceName($deviceName)
            ->setDeviceFingerprint($deviceId)
            ->setDeviceType($deviceType)
            ->setIpAddress($request->getClientIp())
            ->setUserAgent($request->headers->get('User-Agent'))
            ->setRegisteredAt(new \DateTime())
            ->setIsTrusted(true) // oder false → Adminfreigabe nötig
            ->setIsActive(true);
        $this->em->persist($device);
    } else {
        // Metadaten bei erneutem Login aktualisieren
        $device->setIpAddress($request->getClientIp());
        $device->setUserAgent($request->headers->get('User-Agent'));
        $device->setLastLoginAt(new \DateTime());
        $device->setLastSeenAt(new \DateTime());
    }

    // Dummy-Token (später JWT!)
    $token = 'token_' . bin2hex(random_bytes(16));

    $this->em->flush();

    return $this->json([
        'token' => $token,
        'user' => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'lastLogin' => $user->getLastlogindate()?->format(\DateTime::ATOM),
        ],
        'device' => [
            'fingerprint' => $device->getDeviceFingerprint(),
            'name' => $device->getDeviceName(),
            'type' => $device->getDeviceType(),
            'trusted' => $device->isTrusted(),
            'active' => $device->isActive(),
            'lastLogin' => $device->getLastLoginAt()?->format(\DateTime::ATOM),
        ]
    ]);
}



    // 📋 Login-Historie
    #[Route('/user/logins', name: 'user_logins', methods: ['GET'])]
    public function getLoginHistory(Request $request, UserLoginHistoryRepository $repo): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 10);
        $logins = $repo->createQueryBuilder('l')
            ->orderBy('l.loginAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $data = array_map(fn($entry) => [
            'id' => $entry->getId(),
            'userId' => $entry->getUser()?->getId(),
            'username' => $entry->getUser()?->getUsername(),
            'ip' => $entry->getIpAddress(),
            'userAgent' => $entry->getUserAgent(),
            'success' => $entry->isSuccess(),
            'loginAt' => $entry->getLoginAt()?->format(\DateTime::ATOM),
        ], $logins);

        return $this->json($data);
    }

    // 📡 Aktive Sessions
    #[Route('/user/sessions', name: 'user_sessions', methods: ['GET'])]
    public function getActiveSessions(UserSessionRepository $repo): JsonResponse
    {
        $now = new \DateTime();
        $sessions = $repo->createQueryBuilder('s')
            ->where('s.expiresAt > :now')
            ->setParameter('now', $now)
            ->orderBy('s.startedAt', 'DESC')
            ->getQuery()
            ->getResult();

        $data = array_map(fn($s) => [
            'id' => $s->getId(),
            'userId' => $s->getUser()?->getId(),
            'username' => $s->getUser()?->getUsername(),
            'ip' => $s->getIpAddress(),
            'userAgent' => $s->getUserAgent(),
            'startedAt' => $s->getStartedAt()?->format(\DateTime::ATOM),
            'lastActivity' => $s->getLastActivity()?->format(\DateTime::ATOM),
            'expiresAt' => $s->getExpiresAt()?->format(\DateTime::ATOM),
        ], $sessions);

        return $this->json($data);
    }
}
