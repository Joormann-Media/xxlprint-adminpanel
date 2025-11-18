<?php

namespace App\Entity;

use App\Repository\ConcessionManagerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConcessionManagerRepository::class)]
class ConcessionManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private string $concessionNumber;

    #[ORM\Column(length: 40)]
    private string $type; // e.g. "scheduled", "taxi", "charter"

    #[ORM\Column(length: 100)]
    private string $company;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $validFrom;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $validUntil;

    #[ORM\Column(length: 80)]
    private string $authority;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $issueDate = null;

    #[ORM\Column(length: 20)]
    private string $status = 'active';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $documentFile = null; // Path to PDF/scan

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $vehicleLimit = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    // ... Getter und Setter wie gewohnt ...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConcessionNumber(): ?string
    {
        return $this->concessionNumber;
    }

    public function setConcessionNumber(string $concessionNumber): static
    {
        $this->concessionNumber = $concessionNumber;

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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getValidFrom(): ?\DateTime
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTime $validFrom): static
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function getValidUntil(): ?\DateTime
    {
        return $this->validUntil;
    }

    public function setValidUntil(\DateTime $validUntil): static
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    public function getAuthority(): ?string
    {
        return $this->authority;
    }

    public function setAuthority(string $authority): static
    {
        $this->authority = $authority;

        return $this;
    }

    public function getIssueDate(): ?\DateTime
    {
        return $this->issueDate;
    }

    public function setIssueDate(?\DateTime $issueDate): static
    {
        $this->issueDate = $issueDate;

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

    public function getDocumentFile(): ?string
    {
        return $this->documentFile;
    }

    public function setDocumentFile(?string $documentFile): static
    {
        $this->documentFile = $documentFile;

        return $this;
    }

    public function getVehicleLimit(): ?int
    {
        return $this->vehicleLimit;
    }

    public function setVehicleLimit(?int $vehicleLimit): static
    {
        $this->vehicleLimit = $vehicleLimit;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}