<?php

namespace App\EventListener;

use App\Entity\UserLoginHistory;
use App\Entity\UserSession;
use App\Entity\User;
use App\Entity\UserOnlineStatus;
use App\Repository\SessionTableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Predis\Client;

#[AsEventListener(event: SecurityEvents::INTERACTIVE_LOGIN)]
class LoginSuccessListener
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
        private SessionTableRepository $sessionTableRepo,
    ) {}

    public function __invoke(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $request = $this->requestStack->getMainRequest();
        $session = $request?->getSession();

        if (!$user || !$session || !method_exists($user, 'getId')) {
            return;
        }

        $sessionId = $session->getId();
        $ip = $request->getClientIp();
        $userAgent = $request->headers->get('User-Agent');
        $now = new \DateTime();

        // 1. Letzten Login speichern
        $user->setLastlogindate($now);

        // 2. Login History
        $login = new UserLoginHistory();
        $login->setUser($user);
        $login->setLoginAt($now);
        $login->setIpAddress($ip);
        $login->setUserAgent($userAgent);
        $this->em->persist($login);

        // 3. Neue Session-Eintrag
        $userSession = new UserSession();
        $userSession->setUser($user);
        $userSession->setSessionId($sessionId);
        $userSession->setCreatedAt($now);
        $userSession->setLastActiveAt($now);
        $userSession->setIp($ip);
        $userSession->setUserAgent($userAgent);
        $userSession->setIsActive(true);
        $this->em->persist($userSession);

        // 4. Online-Status-Entität
        $onlineStatus = $user->getOnlineStatus();
        if (!$onlineStatus) {
            $onlineStatus = new UserOnlineStatus();
            $onlineStatus->setUser($user);
            $user->setOnlineStatus($onlineStatus);
            $this->em->persist($onlineStatus);
        }

        $onlineStatus->setIsOnline(true);
        $onlineStatus->setLastSeenAt($now);

        // 5. Redis setzen (via Predis)
        try {
            $redis = new Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379,
                'password' => 'DjTmKJc310810090210',
            ]);
            $redis->setex('user_online:' . $user->getId(), 600, '1');
        } catch (\Exception $e) {
            // Logging oder ignorieren
        }

        // 6. Daten speichern
        $this->em->flush();

        // 7. Session-Tabelle updaten (symfony_sessions → User-ID setzen)
        usleep(500000); // 0,5 Sek zur Sicherheit
        $this->sessionTableRepo->updateUserIdForSession($sessionId, $user->getId());
    }
}
