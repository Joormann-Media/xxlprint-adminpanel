<?php

namespace App\Entity;

use App\Repository\ReadmeManagerRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReadmeManagerRepository::class)]
class ReadmeManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    // Ziel (z.B. Entity-Name oder Modul)
    #[ORM\Column(type: Types::STRING, length: 128)]
    #[Assert\NotBlank]
    private ?string $target = null;

    // Optional: Für spezifische Einträge (z.B. Entitäts-ID)
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $targetId = null;

    // Titel für die Readme
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $title = null;

    // Inhalt als Text (z.B. Markdown, HTML)
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $content = null;

    // Status: active, archived, etc.
    #[ORM\Column(type: Types::STRING, length: 32, options: ['default' => 'active'])]
    private ?string $status = 'active';

    // Erstellt am
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    // Autor/Ersteller (User)
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?User $author = null;

    // Minimale Rolle zum Lesen/Bearbeiten
    #[ORM\ManyToOne(targetEntity: UserRoles::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?UserRoles $minRole = null;

    // --- Getter & Setter ---

    public function getId(): ?int { return $this->id; }

    public function getTarget(): ?string { return $this->target; }
    public function setTarget(string $target): static { $this->target = $target; return $this; }

    public function getTargetId(): ?int { return $this->targetId; }
    public function setTargetId(?int $targetId): static { $this->targetId = $targetId; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): static { $this->title = $title; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): static { $this->content = $content; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getAuthor(): ?User { return $this->author; }
    public function setAuthor(?User $author): static { $this->author = $author; return $this; }

    public function getMinRole(): ?UserRoles { return $this->minRole; }
    public function setMinRole(?UserRoles $minRole): static { $this->minRole = $minRole; return $this; }
}
