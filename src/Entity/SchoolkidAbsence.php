<?php

namespace App\Entity;

use App\Repository\SchoolkidAbsenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SchoolkidAbsenceRepository::class)]
class SchoolkidAbsence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Schoolkids::class, inversedBy: 'absences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Schoolkids $schoolkid = null;

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

    // Getter und Setter folgen hier (magst du die generiert haben? Sag Bescheid!)

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAway(): ?\DateTime
    {
        return $this->startAway;
    }

    public function setStartAway(\DateTime $startAway): static
    {
        $this->startAway = $startAway;

        return $this;
    }

    public function getEndAway(): ?\DateTime
    {
        return $this->endAway;
    }

    public function setEndAway(?\DateTime $endAway): static
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

    public function getReportedAt(): ?\DateTime
    {
        return $this->reportedAt;
    }

    public function setReportedAt(\DateTime $reportedAt): static
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

    public function getSchoolkid(): ?Schoolkids
    {
        return $this->schoolkid;
    }

    public function setSchoolkid(?Schoolkids $schoolkid): static
    {
        $this->schoolkid = $schoolkid;

        return $this;
    }
}
