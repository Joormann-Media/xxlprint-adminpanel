<?php

namespace App\Entity;

use App\Repository\VehicleInspectionIntervalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleInspectionIntervalRepository::class)]
class VehicleInspectionInterval
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $inspectionType;

    #[ORM\Column(type: 'integer')]
    private int $intervalMonths;

    #[ORM\Column(type: 'string', length: 100)]
    private string $legalBasis;

    #[ORM\Column(type: 'string', length: 20)]
    private string $mandatory;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateLast = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateNext = null;

    // ... Getter/Setter wie gehabt ...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInspectionType(): ?string
    {
        return $this->inspectionType;
    }

    public function setInspectionType(string $inspectionType): static
    {
        $this->inspectionType = $inspectionType;

        return $this;
    }

    public function getIntervalMonths(): ?int
    {
        return $this->intervalMonths;
    }

    public function setIntervalMonths(int $intervalMonths): static
    {
        $this->intervalMonths = $intervalMonths;

        return $this;
    }

    public function getLegalBasis(): ?string
    {
        return $this->legalBasis;
    }

    public function setLegalBasis(string $legalBasis): static
    {
        $this->legalBasis = $legalBasis;

        return $this;
    }

    public function getMandatory(): ?string
    {
        return $this->mandatory;
    }

    public function setMandatory(string $mandatory): static
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    public function getDateLast(): ?\DateTime
    {
        return $this->dateLast;
    }

    public function setDateLast(?\DateTime $dateLast): static
    {
        $this->dateLast = $dateLast;

        return $this;
    }

    public function getDateNext(): ?\DateTime
    {
        return $this->dateNext;
    }

    public function setDateNext(?\DateTime $dateNext): static
    {
        $this->dateNext = $dateNext;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }
}
