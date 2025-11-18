<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class MileageLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    private ?Employee $driver = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'integer')]
    private int $startMile = 0;

    #[ORM\Column(type: 'integer')]
    private int $endMile = 0;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    private ?string $purpose = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $route = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $startLocation = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $endLocation = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $passengers = null; // Als Text, JSON möglich

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $gasDate = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $gasQuantity = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $refuelReceipt = null; // Pfad zum Tankbeleg-Foto

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $adBlueDate = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $adBlueQuantity = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $oilDate = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $oilQuantity = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $washerFluidDate = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $washerFluidQuantity = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $signature = null; // Datei/Scan Unterschrift

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStartMile(): ?int
    {
        return $this->startMile;
    }

    public function setStartMile(int $startMile): static
    {
        $this->startMile = $startMile;

        return $this;
    }

    public function getEndMile(): ?int
    {
        return $this->endMile;
    }

    public function setEndMile(int $endMile): static
    {
        $this->endMile = $endMile;

        return $this;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    public function setPurpose(?string $purpose): static
    {
        $this->purpose = $purpose;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(?string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function getStartLocation(): ?string
    {
        return $this->startLocation;
    }

    public function setStartLocation(?string $startLocation): static
    {
        $this->startLocation = $startLocation;

        return $this;
    }

    public function getEndLocation(): ?string
    {
        return $this->endLocation;
    }

    public function setEndLocation(?string $endLocation): static
    {
        $this->endLocation = $endLocation;

        return $this;
    }

    public function getPassengers(): ?string
    {
        return $this->passengers;
    }

    public function setPassengers(?string $passengers): static
    {
        $this->passengers = $passengers;

        return $this;
    }

    public function getGasDate(): ?\DateTime
    {
        return $this->gasDate;
    }

    public function setGasDate(?\DateTime $gasDate): static
    {
        $this->gasDate = $gasDate;

        return $this;
    }

    public function getGasQuantity(): ?float
    {
        return $this->gasQuantity;
    }

    public function setGasQuantity(?float $gasQuantity): static
    {
        $this->gasQuantity = $gasQuantity;

        return $this;
    }

    public function getRefuelReceipt(): ?string
    {
        return $this->refuelReceipt;
    }

    public function setRefuelReceipt(?string $refuelReceipt): static
    {
        $this->refuelReceipt = $refuelReceipt;

        return $this;
    }

    public function getAdBlueDate(): ?\DateTime
    {
        return $this->adBlueDate;
    }

    public function setAdBlueDate(?\DateTime $adBlueDate): static
    {
        $this->adBlueDate = $adBlueDate;

        return $this;
    }

    public function getAdBlueQuantity(): ?float
    {
        return $this->adBlueQuantity;
    }

    public function setAdBlueQuantity(?float $adBlueQuantity): static
    {
        $this->adBlueQuantity = $adBlueQuantity;

        return $this;
    }

    public function getOilDate(): ?\DateTime
    {
        return $this->oilDate;
    }

    public function setOilDate(?\DateTime $oilDate): static
    {
        $this->oilDate = $oilDate;

        return $this;
    }

    public function getOilQuantity(): ?float
    {
        return $this->oilQuantity;
    }

    public function setOilQuantity(?float $oilQuantity): static
    {
        $this->oilQuantity = $oilQuantity;

        return $this;
    }

    public function getWasherFluidDate(): ?\DateTime
    {
        return $this->washerFluidDate;
    }

    public function setWasherFluidDate(?\DateTime $washerFluidDate): static
    {
        $this->washerFluidDate = $washerFluidDate;

        return $this;
    }

    public function getWasherFluidQuantity(): ?float
    {
        return $this->washerFluidQuantity;
    }

    public function setWasherFluidQuantity(?float $washerFluidQuantity): static
    {
        $this->washerFluidQuantity = $washerFluidQuantity;

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

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): static
    {
        $this->signature = $signature;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

    public function getDriver(): ?Employee
    {
        return $this->driver;
    }

    public function setDriver(?Employee $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    
}
