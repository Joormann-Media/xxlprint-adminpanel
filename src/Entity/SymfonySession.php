<?php

namespace App\Entity;

use App\Repository\SymfonySessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SymfonySessionRepository::class)]
#[ORM\Table(name: 'sessions')]
class SymfonySession
{
    #[ORM\Id]
    #[ORM\Column(name: 'sess_id', type: 'string', length: 128)]
    private string $sessId;

    #[ORM\Column(name: 'sess_data', type: 'blob')]
    private $sessData;

    #[ORM\Column(name: 'sess_lifetime', type: 'integer')]
    private int $sessLifetime;

    #[ORM\Column(name: 'sess_time', type: 'integer')]
    private int $sessTime;

    #[ORM\Column(name: 'user_id', type: 'integer', nullable: true)]
    private ?int $userId = null;

    public function getSessId(): string { return $this->sessId; }
    public function setSessId(string $sessId): self { $this->sessId = $sessId; return $this; }

    public function getSessData(): ?string
    {
        if (is_resource($this->sessData)) {
            return stream_get_contents($this->sessData);
        }
        return $this->sessData;
    }

    public function setSessData($sessData): self
    {
        $this->sessData = $sessData;
        return $this;
    }

    public function getSessLifetime(): int { return $this->sessLifetime; }
    public function setSessLifetime(int $sessLifetime): self { $this->sessLifetime = $sessLifetime; return $this; }

    public function getSessTime(): int { return $this->sessTime; }
    public function setSessTime(int $sessTime): self { $this->sessTime = $sessTime; return $this; }

    public function getUserId(): ?int { return $this->userId; }
    public function setUserId(?int $userId): self { $this->userId = $userId; return $this; }

    public function getExpiresAt(): \DateTimeInterface
    {
        return (new \DateTimeImmutable())->setTimestamp($this->sessTime + $this->sessLifetime);
    }

    public function isExpired(): bool
    {
        return (time() > ($this->sessTime + $this->sessLifetime));
    }
}
