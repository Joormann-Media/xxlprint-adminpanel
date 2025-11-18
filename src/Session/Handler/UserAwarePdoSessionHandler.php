<?php

namespace App\Session\Handler;

use PDO;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class UserAwarePdoSessionHandler extends PdoSessionHandler
{
    private PDO $pdo;

    public function __construct(
        PDO $pdo,
        array $options,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack
    ) {
        parent::__construct($pdo, $options);
        $this->pdo = $pdo; // Speichere PDO für eigene Query
    }

    public function write(string $sessionId, string $data): bool
    {
        $userId = null;

        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        if (is_object($user) && method_exists($user, 'getId')) {
            $userId = $user->getId();
        }

        // Schreibe die Session wie gewohnt
        $result = parent::write($sessionId, $data);

        // Danach user_id ergänzen (wenn vorhanden)
        if ($userId !== null) {
            $stmt = $this->pdo->prepare("
                UPDATE sessions
                SET user_id = :user_id
                WHERE sess_id = :session_id
            ");

            $stmt->execute([
                'user_id' => $userId,
                'session_id' => $sessionId,
            ]);
        }

        return $result;
    }
}
