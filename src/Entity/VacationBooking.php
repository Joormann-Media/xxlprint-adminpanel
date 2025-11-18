<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class VacationBooking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: EmployeeVacation::class, inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?EmployeeVacation $employeeVacation = null;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateTaken; // Wann genommen (Startdatum)

    #[ORM\Column(type: 'float')]
    private float $days = 1; // Anzahl Tage (z.B. 0.5 für halben Tag)

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $reason = null; // z.B. „Erholung“, „Sonderurlaub“, „Kind krank“

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $comment = null;

    // Standard-Getter/Setter...
    public function getId(): ?int { return $this->id; }
    public function getEmployeeVacation(): ?EmployeeVacation { return $this->employeeVacation; }
    public function setEmployeeVacation(?EmployeeVacation $ev): self { $this->employeeVacation = $ev; return $this; }
    public function getDateTaken(): \DateTimeInterface { return $this->dateTaken; }
    public function setDateTaken(\DateTimeInterface $date): self { $this->dateTaken = $date; return $this; }
    public function getDays(): float { return $this->days; }
    public function setDays(float $days): self { $this->days = $days; return $this; }
    public function getReason(): ?string { return $this->reason; }
    public function setReason(?string $reason): self { $this->reason = $reason; return $this; }
    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): self { $this->comment = $comment; return $this; }
}
