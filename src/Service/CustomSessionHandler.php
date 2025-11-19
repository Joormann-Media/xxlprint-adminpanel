<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\AbstractSessionHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\DBAL\Connection;

class CustomSessionHandler extends AbstractSessionHandler
{
    private \PDO $pdoConnection;
    private array $options = [];
    private TokenStorageInterface $tokenStorage;
    private RequestStack $requestStack;

    public function __construct(
        Connection $connection,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    ) {
        $this->pdoConnection = $connection->getNativeConnection();
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    /**
     * PHP REQUIRED: open() für SessionHandlerInterface
     * Wird von PHP/Symfony nicht wirklich benutzt, muss aber existieren.
     */
    public function open(string $savePath, string $sessionName): bool
    {
        // Pflichtaufruf
        parent::open($savePath, $sessionName);

        // Optional Logging
        $this->options['gc_maxlifetime'] = ini_get('session.gc_maxlifetime');

        return true;
    }

    /**
     * PHP REQUIRED: close() für SessionHandlerInterface
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * SESSION LESEN
     */
    protected function doRead(string $sessionId): string
    {
        $stmt = $this->pdoConnection->prepare(
            'SELECT sess_data FROM sessions WHERE sess_id = :sess_id'
        );
        $stmt->execute(['sess_id' => $sessionId]);

        $data = $stmt->fetchColumn();

        // wenn keine session → leerer string
        return ($data === false || $data === null) ? '' : (string) $data;
    }

    /**
     * SESSION SCHREIBEN
     */
    protected function doWrite(string $sessionId, string $data): bool
    {
        $lifetime = $this->options['gc_maxlifetime']
            ?? intval(ini_get('session.gc_maxlifetime'))
            ?? 1440;

        $stmt = $this->pdoConnection->prepare(
            'INSERT INTO sessions (sess_id, sess_data, sess_lifetime, sess_time)
             VALUES (:sess_id, :sess_data, :sess_lifetime, :sess_time)
             ON DUPLICATE KEY UPDATE
                 sess_data = VALUES(sess_data),
                 sess_lifetime = VALUES(sess_lifetime),
                 sess_time = VALUES(sess_time)'
        );

        return $stmt->execute([
            'sess_id'       => $sessionId,
            'sess_data'     => $data,
            'sess_lifetime' => $lifetime,
            'sess_time'     => time(),
        ]);
    }

    /**
     * SESSION LÖSCHEN
     */
    protected function doDestroy(string $sessionId): bool
    {
        $stmt = $this->pdoConnection->prepare(
            'DELETE FROM sessions WHERE sess_id = :sess_id'
        );
        return $stmt->execute(['sess_id' => $sessionId]);
    }

    /**
     * GARBAGE COLLECTION
     */
    public function gc(int $maxlifetime): int|false
    {
        $stmt = $this->pdoConnection->prepare(
            'DELETE FROM sessions WHERE sess_time < :time'
        );
        $stmt->execute(['time' => time() - $maxlifetime]);
        return $stmt->rowCount();
    }

    /**
     * TIMESTAMP UPDATE
     */
    public function updateTimestamp(string $sessionId, string $data): bool
    {
        $stmt = $this->pdoConnection->prepare(
            'UPDATE sessions SET sess_time = :t WHERE sess_id = :id'
        );
        return $stmt->execute([
            't'  => time(),
            'id' => $sessionId,
        ]);
    }

    /**
     * USER/IP/AGENT EINTRAGEN + LOG
     */
    public function write($sessionId, $data): bool
    {
        $result = parent::write($sessionId, $data);

        try {
            $request = $this->requestStack->getCurrentRequest();
            $token   = $this->tokenStorage->getToken();

            $user = ($token && is_object($token->getUser()) && method_exists($token->getUser(), 'getId'))
                ? $token->getUser()
                : null;

            $userId    = $user?->getId() ?? null;
            $ipAddress = $request?->getClientIp() ?? null;
            $agent     = $request?->headers->get('User-Agent') ?? null;

            if ($userId || $ipAddress || $agent) {
                $stmt = $this->pdoConnection->prepare('
                    UPDATE sessions
                    SET user_id = :uid,
                        ip_address = :ip,
                        user_agent = :ua
                    WHERE sess_id = :id
                ');

                $stmt->execute([
                    'uid' => $userId,
                    'ip'  => $ipAddress,
                    'ua'  => $agent,
                    'id'  => $sessionId,
                ]);
            }

        } catch (\Throwable $e) {
            file_put_contents(
                dirname(__DIR__, 2) . '/var/session_debug.txt',
                "ERROR: " . $e->getMessage() . "\n",
                FILE_APPEND
            );
        }

        return $result;
    }
}
