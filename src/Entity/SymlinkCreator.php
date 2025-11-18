<?php

namespace App\Entity;

use App\Repository\SymlinkCreatorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SymlinkCreatorRepository::class)]
class SymlinkCreator
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $sourcePath = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $sourceDestination = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $symlinkCreated = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $symlinkCreatedBy = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $symlinkStatus = null;

    public function __construct()
    {
        $this->symlinkCreated = new \DateTimeImmutable(); // Automatisch beim Erstellen
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourcePath(): ?string
    {
        return $this->sourcePath;
    }

    public function setSourcePath(?string $sourcePath): static
    {
        $this->sourcePath = $sourcePath;
        return $this;
    }

    public function getSourceDestination(): ?string
    {
        return $this->sourceDestination;
    }

    public function setSourceDestination(?string $sourceDestination): static
    {
        $this->sourceDestination = $sourceDestination;
        return $this;
    }

    public function getSymlinkCreated(): ?\DateTimeImmutable
    {
        return $this->symlinkCreated;
    }

    public function setSymlinkCreated(?\DateTimeImmutable $symlinkCreated): static
    {
        $this->symlinkCreated = $symlinkCreated;
        return $this;
    }

    public function getSymlinkCreatedBy(): ?string
    {
        return $this->symlinkCreatedBy;
    }

    public function setSymlinkCreatedBy(?string $symlinkCreatedBy): static
    {
        $this->symlinkCreatedBy = $symlinkCreatedBy;
        return $this;
    }

    public function getSymlinkStatus(): ?string
    {
        return $this->symlinkStatus;
    }

    public function setSymlinkStatus(?string $symlinkStatus): static
    {
        $this->symlinkStatus = $symlinkStatus;
        return $this;
    }
}
