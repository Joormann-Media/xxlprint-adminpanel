<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\AbstractSessionHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CustomSessionHandler extends AbstractSessionHandler
{
    private \PDO $pdoConnection;
    private array $options;
    private $tokenStorage;
    private $requestStack;

    public function __construct(
        \PDO $pdoConnection,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    ) {
        $this->pdoConnection = $pdoConnection;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        // $this->options = $options; // Falls du die brauchst, reaktivieren!
    }

    protected function doRead(string $sessionId): string
    {
        $stmt = $this->pdoConnection->prepare('SELECT sess_data FROM sessions WHERE sess_id = :sess_id');
        $stmt->execute(['sess_id' => $sessionId]);
        return (string) $stmt->fetchColumn();
    }

    protected function doWrite(string $sessionId, string $data): bool
    {
        $stmt = $this->pdoConnection->prepare('
            INSERT INTO sessions (sess_id, sess_data, sess_lifetime, sess_time)
            VALUES (:sess_id, :sess_data, :sess_lifetime, :sess_time)
            ON DUPLICATE KEY UPDATE sess_data = :sess_data, sess_lifetime = :sess_lifetime, sess_time = :sess_time
        ');
        return $stmt->execute([
            'sess_id' => $sessionId,
            'sess_data' => $data,
            'sess_lifetime' => $this->options['gc_maxlifetime'] ?? 1440,
            'sess_time' => time(),
        ]);
    }

    protected function doDestroy(string $sessionId): bool
    {
        $stmt = $this->pdoConnection->prepare('DELETE FROM sessions WHERE sess_id = :sess_id');
        return $stmt->execute(['sess_id' => $sessionId]);
    }

    public function close(): bool
    {
        return true;
    }

    public function gc(int $maxlifetime): int|false
    {
        $stmt = $this->pdoConnection->prepare('DELETE FROM sessions WHERE sess_time < :time');
        $stmt->execute(['time' => time() - $maxlifetime]);
        return $stmt->rowCount();
    }

    public function write($sessionId, $data): bool
    {
        $result = parent::write($sessionId, $data);

        try {
            $log = [
                'called' => true,
                'sessionId' => $sessionId,
                'result' => $result,
            ];

            $request = $this->requestStack->getCurrentRequest();
            $log['hasRequest'] = $request !== null;

            $token = $this->tokenStorage->getToken();
            $log['hasToken'] = $token !== null;

            $user = ($token && is_object($token->getUser()) && method_exists($token->getUser(), 'getId'))
                ? $token->getUser()
                : null;

            $userId = $user ? $user->getId() : null;
            $ipAddress = $request ? $request->getClientIp() : null;
            $userAgent = $request ? $request->headers->get('User-Agent') : null;

            $log['userId'] = $userId;
            $log['ip'] = $ipAddress;
            $log['agent'] = $userAgent;

            if ($userId || $ipAddress || $userAgent) {
                $stmt = $this->pdoConnection->prepare('
                    UPDATE sessions
                    SET user_id = :user_id,
                        ip_address = :ip,
                        user_agent = :ua
                    WHERE sess_id = :sess_id
                ');

                $success = $stmt->execute([
                    'user_id' => $userId,
                    'ip' => $ipAddress,
                    'ua' => $userAgent,
                    'sess_id' => $sessionId,
                ]);

                $log['sql_success'] = $success;
                $log['sql_error'] = $success ? null : $stmt->errorInfo();
            }

            // --- Log ins Symfony-Projekt-Var-Dir ---
            $logPath = dirname(__DIR__, 2) . '/var/session_debug.txt';
            file_put_contents($logPath, json_encode($log, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);
        } catch (\Throwable $e) {
            $logPath = dirname(__DIR__, 2) . '/var/session_debug.txt';
            file_put_contents($logPath, 'ERROR: ' . $e->getMessage() . "\n", FILE_APPEND);
        }

        return $result;
    }

    public function updateTimestamp(string $sessionId, string $data): bool
    {
        $stmt = $this->pdoConnection->prepare('
            UPDATE sessions
            SET sess_time = :sess_time
            WHERE sess_id = :sess_id
        ');
        return $stmt->execute([
            'sess_time' => time(),
            'sess_id' => $sessionId,
        ]);
    }
}
