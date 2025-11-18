<?php

namespace App\Entity;

use App\Repository\SystemBackupStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SystemBackupStatusRepository::class)]
class SystemBackupStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
private ?\DateTimeImmutable $createdAt = null;

#[ORM\Column(length: 50)]
private ?string $type = null;   // z.B. "database", "files", "full", etc.

#[ORM\Column(length: 50)]
private ?string $mode = null;   // z.B. "auto", "manual", "scheduled", etc.

#[ORM\Column(length: 255)]
private ?string $path = null;   // Speicherort des Backups

#[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
private ?User $user = null;     // Optional: Wer hat's gemacht

#[ORM\Column(length: 20)]
private ?string $status = null; // "success", "failed", etc.

public function getId(): ?int
{
    return $this->id;
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

public function getType(): ?string
{
    return $this->type;
}

public function setType(string $type): static
{
    $this->type = $type;

    return $this;
}

public function getMode(): ?string
{
    return $this->mode;
}

public function setMode(string $mode): static
{
    $this->mode = $mode;

    return $this;
}

public function getPath(): ?string
{
    return $this->path;
}

public function setPath(string $path): static
{
    $this->path = $path;

    return $this;
}

public function getStatus(): ?string
{
    return $this->status;
}

public function setStatus(string $status): static
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
