<?php

namespace App\Entity;

use App\Repository\HolidayRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HolidayRepository::class)]
class Holiday
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

     // z. B. "Ostern", "Weihnachtsferien", "Pfingsten"
    #[ORM\Column(length: 100)]
    private string $name;

    // Typ: holiday, vacation (ferientyp), usw.
    #[ORM\Column(length: 30)]
    private string $type; // holiday | school_vacation | custom

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $startDate;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $endDate;

    // Wiederholung: none, yearly, fixed-date (z. B. 03.10.), moveable (Ostern)
    #[ORM\Column(length: 20, options: ['default' => 'none'])]
    private string $recurrence = 'none'; // none | yearly | easter_relative

    // Relativer Versatz zu Ostern/Pfingsten usw. als Integer (z. B. +1/-2 Tage)
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $daysOffset = null;

    // optional: Bemerkung/Kommentar
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $comment = null;

    // Bundesland-Bezug (optional: NULL = bundesweit)
    #[ORM\ManyToOne(targetEntity: State::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?State $state = null;

    // Getter/Setter wie gehabt …

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getRecurrence(): ?string
    {
        return $this->recurrence;
    }

    public function setRecurrence(string $recurrence): static
    {
        $this->recurrence = $recurrence;

        return $this;
    }

    public function getDaysOffset(): ?int
    {
        return $this->daysOffset;
    }

    public function setDaysOffset(?int $daysOffset): static
    {
        $this->daysOffset = $daysOffset;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): static
    {
        $this->state = $state;

        return $this;
    }
}
