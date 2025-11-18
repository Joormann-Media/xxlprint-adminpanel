<?php

namespace App\Entity;

use App\Repository\VacationManagerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VacationManagerRepository::class)]
class VacationManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $vacationStatus = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vacationExpires = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vacationCreate = null;

    #[ORM\Column(length: 255)]
    private ?string $vacationUser = null;

    #[ORM\Column(length: 255)]
    private ?string $vacationDescription = null;

    #[ORM\Column(length: 255)]
    private ?string $vacationContent = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $vacationStart = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVacationStatus(): ?string
    {
        return $this->vacationStatus;
    }

    public function setVacationStatus(string $vacationStatus): static
    {
        $this->vacationStatus = $vacationStatus;

        return $this;
    }

    public function getVacationExpires(): ?\DateTimeInterface
    {
        return $this->vacationExpires;
    }

    public function setVacationExpires(\DateTimeInterface $vacationExpires): static
    {
        $this->vacationExpires = $vacationExpires;

        return $this;
    }

    public function getVacationCreate(): ?\DateTimeInterface
    {
        return $this->vacationCreate;
    }

    public function setVacationCreate(\DateTimeInterface $vacationCreate): static
    {
        $this->vacationCreate = $vacationCreate;

        return $this;
    }

    public function getVacationUser(): ?string
    {
        return $this->vacationUser;
    }

    public function setVacationUser(string $vacationUser): static
    {
        $this->vacationUser = $vacationUser;

        return $this;
    }

    public function getVacationDescription(): ?string
    {
        return $this->vacationDescription;
    }

    public function setVacationDescription(string $vacationDescription): static
    {
        $this->vacationDescription = $vacationDescription;

        return $this;
    }

    public function getVacationContent(): ?string
    {
        return $this->vacationContent;
    }

    public function setVacationContent(string $vacationContent): static
    {
        $this->vacationContent = $vacationContent;

        return $this;
    }

    public function getVacationStart(): ?\DateTimeInterface
    {
        return $this->vacationStart;
    }

    public function setVacationStart(\DateTimeInterface $vacationStart): static
    {
        $this->vacationStart = $vacationStart;

        return $this;
    }
}
