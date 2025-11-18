<?php

namespace App\Entity;

use App\Repository\EmployeeAbsenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeAbsenceRepository::class)]
class EmployeeAbsence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'absences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $startAway = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $endAway = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reasonAway = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $reportedBy = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\Choice(choices: ['Phone', 'Email', 'WhatsApp', 'Messenger', 'Smoke signal', 'Pigeon', 'Letter', 'Other'])]
    private ?string $reportMethod = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $receivedBy = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $reportedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // --- Getter & Setter ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;
        return $this;
    }

    public function getStartAway(): ?\DateTimeInterface
    {
        return $this->startAway;
    }

    public function setStartAway(\DateTimeInterface $startAway): static
    {
        $this->startAway = $startAway;
        return $this;
    }

    public function getEndAway(): ?\DateTimeInterface
    {
        return $this->endAway;
    }

    public function setEndAway(?\DateTimeInterface $endAway): static
    {
        $this->endAway = $endAway;
        return $this;
    }

    public function getReasonAway(): ?string
    {
        return $this->reasonAway;
    }

    public function setReasonAway(?string $reasonAway): static
    {
        $this->reasonAway = $reasonAway;
        return $this;
    }

    public function getReportedBy(): ?string
    {
        return $this->reportedBy;
    }

    public function setReportedBy(string $reportedBy): static
    {
        $this->reportedBy = $reportedBy;
        return $this;
    }

    public function getReportMethod(): ?string
    {
        return $this->reportMethod;
    }

    public function setReportMethod(string $reportMethod): static
    {
        $this->reportMethod = $reportMethod;
        return $this;
    }

    public function getReceivedBy(): ?string
    {
        return $this->receivedBy;
    }

    public function setReceivedBy(?string $receivedBy): static
    {
        $this->receivedBy = $receivedBy;
        return $this;
    }

    public function getReportedAt(): ?\DateTimeInterface
    {
        return $this->reportedAt;
    }

    public function setReportedAt(\DateTimeInterface $reportedAt): static
    {
        $this->reportedAt = $reportedAt;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}