<?php

namespace App\Entity;

use App\Repository\BackupManagerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BackupManagerRepository::class)]
class BackupManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

     #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 32)]
    private ?string $type = null; // 'sql', 'project', 'git', 'full'

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $pathSql = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $pathProject = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $gitRemoteStatus = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $gitStatusTimestamp = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $gitStatusMessage = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getter/Setter hier (kannst du über make:entity generieren lassen)

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
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

    public function getPathSql(): ?string
    {
        return $this->pathSql;
    }

    public function setPathSql(?string $pathSql): static
    {
        $this->pathSql = $pathSql;

        return $this;
    }

    public function getPathProject(): ?string
    {
        return $this->pathProject;
    }

    public function setPathProject(?string $pathProject): static
    {
        $this->pathProject = $pathProject;

        return $this;
    }

    public function getGitRemoteStatus(): ?string
    {
        return $this->gitRemoteStatus;
    }

    public function setGitRemoteStatus(?string $gitRemoteStatus): static
    {
        $this->gitRemoteStatus = $gitRemoteStatus;

        return $this;
    }

    public function getGitStatusTimestamp(): ?\DateTime
    {
        return $this->gitStatusTimestamp;
    }

    public function setGitStatusTimestamp(?\DateTime $gitStatusTimestamp): static
    {
        $this->gitStatusTimestamp = $gitStatusTimestamp;

        return $this;
    }

    public function getGitStatusMessage(): ?string
    {
        return $this->gitStatusMessage;
    }

    public function setGitStatusMessage(?string $gitStatusMessage): static
    {
        $this->gitStatusMessage = $gitStatusMessage;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }
}
