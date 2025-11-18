<?php

namespace App\Entity;

use App\Repository\SchoolDepartureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolDepartureRepository::class)]
class SchoolDeparture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'departures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\Column(length: 20)]
    private ?string $weekday = null; // z.B. 'Monday', 'All'

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $departureTime = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $readyTime = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $busLine = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $specialDeparture = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeekday(): ?string
    {
        return $this->weekday;
    }

    public function setWeekday(string $weekday): static
    {
        $this->weekday = $weekday;

        return $this;
    }

    public function getDepartureTime(): ?\DateTime
    {
        return $this->departureTime;
    }

    public function setDepartureTime(\DateTime $departureTime): static
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    public function getReadyTime(): ?\DateTime
    {
        return $this->readyTime;
    }

    public function setReadyTime(?\DateTime $readyTime): static
    {
        $this->readyTime = $readyTime;

        return $this;
    }

    public function getBusLine(): ?string
    {
        return $this->busLine;
    }

    public function setBusLine(?string $busLine): static
    {
        $this->busLine = $busLine;

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

    public function isSpecialDeparture(): bool
    {
        return $this->specialDeparture;
    }

    public function setSpecialDeparture(bool $specialDeparture): static
    {
        $this->specialDeparture = $specialDeparture;
        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): static
    {
        $this->school = $school;

        return $this;
    }

}
