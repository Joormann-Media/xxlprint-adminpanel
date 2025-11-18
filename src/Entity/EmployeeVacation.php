<?php

namespace App\Entity;

use App\Repository\EmployeeVacationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeVacationRepository::class)]
class EmployeeVacation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Employee $employee = null;

    #[ORM\Column(type: 'integer')]
    private int $year;

    #[ORM\Column(type: 'float')]
    private float $total = 0; // Gesamtkontingent (Tage)

    #[ORM\Column(type: 'float')]
    private float $used = 0; // Bereits genommen (Tage)

    #[ORM\Column(type: 'float')]
    private float $remaining = 0; // Resturlaub (Tage), wird berechnet

    #[ORM\OneToMany(mappedBy: "employeeVacation", targetEntity: VacationBooking::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $bookings; // Einzelne Buchungen

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $remark = null;
    

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    // Standard-Getter/Setter...

    public function getId(): ?int { return $this->id; }
    public function getEmployee(): ?Employee { return $this->employee; }
    public function setEmployee(?Employee $employee): self { $this->employee = $employee; return $this; }
    public function getYear(): int { return $this->year; }
    public function setYear(int $year): self { $this->year = $year; return $this; }
    public function getTotal(): float { return $this->total; }
    public function setTotal(float $total): self { $this->total = $total; return $this; }
    public function getUsed(): float { return $this->used; }
    public function setUsed(float $used): self { $this->used = $used; return $this; }
    public function getRemaining(): float { return $this->total - $this->used; }
    public function getRemark(): ?string { return $this->remark; }
    public function setRemark(?string $remark): self { $this->remark = $remark; return $this; }
    
    /**
     * @return Collection<int, VacationBooking>
     */
    public function getBookings(): Collection { return $this->bookings; }
    public function addBooking(VacationBooking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setEmployeeVacation($this);
        }
        return $this;
    }
    public function removeBooking(VacationBooking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getEmployeeVacation() === $this) {
                $booking->setEmployeeVacation(null);
            }
        }
        return $this;
    }
}