<?php

namespace App\Entity;

use App\Repository\AiManagerRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AiManagerRepository::class)]
class AiManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    #[Assert\NotBlank]
    private ?string $aiName = null;

    #[ORM\Column(type: Types::STRING, length: 128, nullable: true)]
    private ?string $aiModel = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $aiSetup = null; // Hier Infos zu Python, Abhängigkeiten, Versionen etc.

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $aiPath = null; // Speicher- oder Startpfad

    #[ORM\Column(type: Types::STRING, length: 128, nullable: true)]
    private ?string $aiHost = null; // Hostname, IP, etc.

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $aiDescription = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $aiCreated = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?User $aiMaintainer = null; // Wer pflegt das Ding

    #[ORM\ManyToOne(targetEntity: UserRoles::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?UserRoles $aiMinrole = null; // Minimale User-Rolle für Zugriff

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $aiAvatar = null; // Avatar-Bildpfad o.ä.

    #[ORM\Column(type: Types::STRING, length: 32, nullable: false, options: ['default' => 'active'])]
    private ?string $aiStatus = 'active'; // active, maintenance, offline, error

    #[ORM\Column(type: Types::STRING, length: 32, nullable: false)]
    private ?string $aiCategory = null; // llm, tts, trainer, image, etc.

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $aiHealthUrl = null; // z.B. http://localhost:11434/health

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $aiLastCheckedAt = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    private ?string $aiApiToken = null; // Optional, falls API mit Auth

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $aiLastResponseMs = null; // letzte Antwortzeit in ms

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $aiLastError = null; // Fehlertext vom letzten Check

    // --- Getter & Setter (so wie du’s gewohnt bist) ---

    public function getId(): ?int { return $this->id; }

    public function getAiName(): ?string { return $this->aiName; }
    public function setAiName(string $aiName): static { $this->aiName = $aiName; return $this; }

    public function getAiModel(): ?string { return $this->aiModel; }
    public function setAiModel(?string $aiModel): static { $this->aiModel = $aiModel; return $this; }

    public function getAiSetup(): ?string { return $this->aiSetup; }
    public function setAiSetup(?string $aiSetup): static { $this->aiSetup = $aiSetup; return $this; }

    public function getAiPath(): ?string { return $this->aiPath; }
    public function setAiPath(?string $aiPath): static { $this->aiPath = $aiPath; return $this; }

    public function getAiHost(): ?string { return $this->aiHost; }
    public function setAiHost(?string $aiHost): static { $this->aiHost = $aiHost; return $this; }

    public function getAiDescription(): ?string { return $this->aiDescription; }
    public function setAiDescription(?string $aiDescription): static { $this->aiDescription = $aiDescription; return $this; }

    public function getAiCreated(): ?\DateTimeImmutable { return $this->aiCreated; }
    public function setAiCreated(\DateTimeImmutable $aiCreated): static { $this->aiCreated = $aiCreated; return $this; }

    public function getAiMaintainer(): ?User { return $this->aiMaintainer; }
    public function setAiMaintainer(?User $aiMaintainer): static { $this->aiMaintainer = $aiMaintainer; return $this; }

    public function getAiMinrole(): ?UserRoles { return $this->aiMinrole; }
    public function setAiMinrole(?UserRoles $aiMinrole): static { $this->aiMinrole = $aiMinrole; return $this; }

    public function getAiAvatar(): ?string
    {
        return $this->aiAvatar;
    }

    public function setAiAvatar(?string $aiAvatar): static
    {
        $this->aiAvatar = $aiAvatar;
        return $this;
    }

    public function getAiStatus(): ?string
    {
        return $this->aiStatus;
    }

    public function setAiStatus(string $aiStatus): static
    {
        $this->aiStatus = $aiStatus;

        return $this;
    }

    public function getAiCategory(): ?string
    {
        return $this->aiCategory;
    }

    public function setAiCategory(string $aiCategory): static
    {
        $this->aiCategory = $aiCategory;

        return $this;
    }

    public function getAiHealthUrl(): ?string
    {
        return $this->aiHealthUrl;
    }

    public function setAiHealthUrl(?string $aiHealthUrl): static
    {
        $this->aiHealthUrl = $aiHealthUrl;

        return $this;
    }

    public function getAiLastCheckedAt(): ?\DateTimeImmutable
    {
        return $this->aiLastCheckedAt;
    }

    public function setAiLastCheckedAt(?\DateTimeImmutable $aiLastCheckedAt): static
    {
        $this->aiLastCheckedAt = $aiLastCheckedAt;

        return $this;
    }

    public function getAiApiToken(): ?string
    {
        return $this->aiApiToken;
    }

    public function setAiApiToken(?string $aiApiToken): static
    {
        $this->aiApiToken = $aiApiToken;

        return $this;
    }

    public function getAiLastResponseMs(): ?int
    {
        return $this->aiLastResponseMs;
    }

    public function setAiLastResponseMs(?int $aiLastResponseMs): static
    {
        $this->aiLastResponseMs = $aiLastResponseMs;

        return $this;
    }

    public function getAiLastError(): ?string
    {
        return $this->aiLastError;
    }

    public function setAiLastError(?string $aiLastError): static
    {
        $this->aiLastError = $aiLastError;

        return $this;
    }
}
