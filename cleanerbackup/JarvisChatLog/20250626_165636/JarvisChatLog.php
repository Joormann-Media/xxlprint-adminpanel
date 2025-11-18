<?php

namespace App\Entity;

use App\Repository\JarvisChatLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JarvisChatLogRepository::class)]
class JarvisChatLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    // ManyToOne-Beziehung zu User (nullable)
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?User $user = null;

    // Nutzeranfrage, max 4096 Zeichen
    #[ORM\Column(type: Types::TEXT, length: 4096)]
    #[Assert\Length(max: 4096)]
    private ?string $prompt = null;

    // Jarvis-Antwort, max 8192 Zeichen
    #[ORM\Column(type: Types::TEXT, length: 8192)]
    #[Assert\Length(max: 8192)]
    private ?string $response = null;

    // Erstelldatum (datetime_immutable)
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    // Optionales Statusfeld (max 32 Zeichen)
    #[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
    #[Assert\Length(max: 32)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrompt(): ?string
    {
        return $this->prompt;
    }

    public function setPrompt(string $prompt): static
    {
        $this->prompt = $prompt;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(string $response): static
    {
        $this->response = $response;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
