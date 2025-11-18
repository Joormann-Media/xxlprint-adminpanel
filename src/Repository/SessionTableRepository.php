<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;

class SessionTableRepository
{
    public function __construct(
        private Connection $connection
    ) {}

    /**
     * ✅ Aktive Sessions abfragen (optional mit User-Filter)
     */
    public function findActiveSessions(): array
    {
        $sql = <<<SQL
            SELECT sess_id, sess_lifetime, sess_time, user_id
            FROM sessions
            WHERE (sess_time + sess_lifetime) > UNIX_TIMESTAMP()
            ORDER BY sess_time DESC
        SQL;

        return $this->connection->fetchAllAssociative($sql);
    }

    /**
     * ✅ Session anhand der ID holen
     */
    public function findBySessionId(string $sessionId): ?array
    {
        return $this->connection->fetchAssociative(
            'SELECT * FROM sessions WHERE sess_id = :id',
            ['id' => $sessionId]
        ) ?: null;
    }

    /**
     * ✅ Session anhand der ID löschen
     */
    public function deleteBySessionId(string $sessionId): int
    {
        return $this->connection->executeStatement(
            'DELETE FROM sessions WHERE sess_id = :id',
            ['id' => $sessionId]
        );
    }

    /**
     * ✅ Alle Sessions eines Users löschen
     */
    public function deleteByUserId(int $userId): int
    {
        return $this->connection->executeStatement(
            'DELETE FROM sessions WHERE user_id = :userId',
            ['userId' => $userId]
        );
    }


    /**
     * ✅ Session aktualisieren (z. B. user_id nach Login setzen)
     */
    public function updateUserIdForSession(string $sessionId, int $userId): void
    {
        $this->connection->executeStatement(
            'UPDATE sessions SET user_id = :userId WHERE sess_id = :sessionId',
            [
                'userId' => $userId,
                'sessionId' => $sessionId,
            ]
        );
    }

    public function findExpiredSessions(?\DateTime $olderThan = null): array
{
    $cutoffTs = $olderThan ? $olderThan->getTimestamp() : time();
    return $this->connection->fetchAllAssociative(
        'SELECT * FROM sessions WHERE sess_time + sess_lifetime < :cutoff',
        ['cutoff' => $cutoffTs]
    );
}

public function deleteExpiredSessions(?\DateTime $olderThan = null): int
{
    $cutoffTs = $olderThan ? $olderThan->getTimestamp() : time();
    return $this->connection->executeStatement(
        'DELETE FROM sessions WHERE sess_time + sess_lifetime < :cutoff',
        ['cutoff' => $cutoffTs]
    );
}


}
