<?php

namespace App\EventListener;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;

#[AsEventListener(event: LogoutEvent::class)]
class UserLogoutListener
{
    private LoggerInterface $logger;

    public function __construct(
        private Connection $connection,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function __invoke(LogoutEvent $event): void
    {
        $sessionId = $event->getRequest()->cookies->get(session_name());

        if (!$sessionId) {
            $this->logger->warning('Logout: Session ID leer', [
                'event' => 'logout',
                'sid' => null,
            ]);
            return;
        }

        // Log
        $log = [
            'event' => 'logout',
            'sid' => $sessionId,
        ];

        // Lösche NUR aus deiner eigenen Session-Tabelle
        $this->connection->executeStatement(
            'DELETE FROM sessions WHERE sess_id = :sid',
            ['sid' => $sessionId]
        );

        $log['status'] = 'deleted';
        $this->logger->info('Logout: Session erfolgreich gelöscht', $log);
    }
}
