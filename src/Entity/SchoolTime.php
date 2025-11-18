<?php

namespace App\Entity;

use App\Repository\SchoolTimeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolTimeRepository::class)]
class SchoolTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'schoolTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $weekday = null; // "Monday", "Tuesday", "All", etc.

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $schoolStart = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $arrivalTime = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $schoolEnd = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $breaks = null; // Beispiel: [{"start": "09:35", "end": "09:50"}, ...]

    // ... plus Getter & Setter für alle Felder

    public function getId(): ?int
    {
        return $this->id;
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

    public function getWeekday(): ?string
    {
        return $this->weekday;
    }

    public function setWeekday(?string $weekday): static
    {
        $this->weekday = $weekday;
        return $this;
    }

    public function getSchoolStart(): ?\DateTimeInterface
    {
        return $this->schoolStart;
    }

    public function setSchoolStart(?\DateTimeInterface $schoolStart): static
    {
        $this->schoolStart = $schoolStart;
        return $this;
    }

    public function getArrivalTime(): ?\DateTimeInterface
    {
        return $this->arrivalTime;
    }

    public function setArrivalTime(?\DateTimeInterface $arrivalTime): static
    {
        $this->arrivalTime = $arrivalTime;
        return $this;
    }

    public function getSchoolEnd(): ?\DateTimeInterface
    {
        return $this->schoolEnd;
    }

    public function setSchoolEnd(?\DateTimeInterface $schoolEnd): static
    {
        $this->schoolEnd = $schoolEnd;
        return $this;
    }

    public function getBreaks(): ?array
    {
        return $this->breaks;
    }

    public function setBreaks(?array $breaks): static
    {
        $this->breaks = $breaks;
        return $this;
    }
}
