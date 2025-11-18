<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OpeningHours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 10)]
    private string $day; // Tag der Woche (Monday, Tuesday, ...)

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $morningStart = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $morningEnd = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $afternoonStart = null;

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $afternoonEnd = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getMorningStart(): ?\DateTimeInterface
    {
        return $this->morningStart;
    }

    public function setMorningStart(?\DateTimeInterface $morningStart): self
    {
        $this->morningStart = $morningStart;

        return $this;
    }

    public function getMorningEnd(): ?\DateTimeInterface
    {
        return $this->morningEnd;
    }

    public function setMorningEnd(?\DateTimeInterface $morningEnd): self
    {
        $this->morningEnd = $morningEnd;

        return $this;
    }

    public function getAfternoonStart(): ?\DateTimeInterface
    {
        return $this->afternoonStart;
    }

    public function setAfternoonStart(?\DateTimeInterface $afternoonStart): self
    {
        $this->afternoonStart = $afternoonStart;

        return $this;
    }

    public function getAfternoonEnd(): ?\DateTimeInterface
    {
        return $this->afternoonEnd;
    }

    public function setAfternoonEnd(?\DateTimeInterface $afternoonEnd): self
    {
        $this->afternoonEnd = $afternoonEnd;

        return $this;
    }
}
