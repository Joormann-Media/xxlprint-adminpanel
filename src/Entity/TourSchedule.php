<?php

namespace App\Entity;

use App\Repository\TourScheduleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourScheduleRepository::class)]
class TourSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Tour::class, inversedBy: 'schedules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tour $tour = null;

    #[ORM\Column(type: 'integer')]
    private ?int $year = null; // Planungsjahr, z.B. 2025

    #[ORM\Column(type: 'json')]
    private array $weekdays = []; // z.B. [1,2,3,4,5] für Mo-Fr (1=Mo ... 7=So)

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $onlyWeekdays = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $onlyWeekend = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $onlyDuringHolidays = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $notDuringHolidays = false;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateFrom = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateTo = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $specialDates = null; // Einzelne Sondertage ["2025-12-24", "2025-05-01"]

    // ... Hier folgen Getter & Setter ...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getWeekdays(): array
    {
        return $this->weekdays;
    }

    public function setWeekdays(array $weekdays): static
    {
        $this->weekdays = $weekdays;

        return $this;
    }

    public function isOnlyWeekdays(): ?bool
    {
        return $this->onlyWeekdays;
    }

    public function setOnlyWeekdays(bool $onlyWeekdays): static
    {
        $this->onlyWeekdays = $onlyWeekdays;

        return $this;
    }

    public function isOnlyWeekend(): ?bool
    {
        return $this->onlyWeekend;
    }

    public function setOnlyWeekend(bool $onlyWeekend): static
    {
        $this->onlyWeekend = $onlyWeekend;

        return $this;
    }

    public function isOnlyDuringHolidays(): ?bool
    {
        return $this->onlyDuringHolidays;
    }

    public function setOnlyDuringHolidays(bool $onlyDuringHolidays): static
    {
        $this->onlyDuringHolidays = $onlyDuringHolidays;

        return $this;
    }

    public function isNotDuringHolidays(): ?bool
    {
        return $this->notDuringHolidays;
    }

    public function setNotDuringHolidays(bool $notDuringHolidays): static
    {
        $this->notDuringHolidays = $notDuringHolidays;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTime $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTime $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getDateFrom(): ?\DateTime
    {
        return $this->dateFrom;
    }

    public function setDateFrom(?\DateTime $dateFrom): static
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTime
    {
        return $this->dateTo;
    }

    public function setDateTo(?\DateTime $dateTo): static
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getSpecialDates(): ?array
    {
        return $this->specialDates;
    }

    public function setSpecialDates(?array $specialDates): static
    {
        $this->specialDates = $specialDates;

        return $this;
    }

    public function getTour(): ?Tour
    {
        return $this->tour;
    }

    public function setTour(?Tour $tour): static
    {
        $this->tour = $tour;

        return $this;
    }
}
