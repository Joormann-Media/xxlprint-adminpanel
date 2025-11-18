<?php

namespace App\Entity;

use App\Repository\VehicleTrackingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleTrackingRepository::class)]
class VehicleTracking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $timestamp;

    #[ORM\Column(type: 'float')]
    private float $latitude;

    #[ORM\Column(type: 'float')]
    private float $longitude;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $speed = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $course = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $postalcode = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $display = null;

    #[ORM\Column(type: 'string', length: 32, options: ['default' => 'unknown'])]
    private string $kmCounter = 'unknown';

    #[ORM\ManyToOne(targetEntity: Vehicle::class, inversedBy: 'trackings')]
    private ?Vehicle $vehicle = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $driverId = null;   // <-- Nur String, keine Relation mehr


    // --- Getter/Setter ---

    public function getId(): ?int { return $this->id; }

    public function getTimestamp(): \DateTimeInterface { return $this->timestamp; }
    public function setTimestamp(\DateTimeInterface $timestamp): self { $this->timestamp = $timestamp; return $this; }

    public function getLatitude(): float { return $this->latitude; }
    public function setLatitude(float $latitude): self { $this->latitude = $latitude; return $this; }

    public function getLongitude(): float { return $this->longitude; }
    public function setLongitude(float $longitude): self { $this->longitude = $longitude; return $this; }

    public function getSpeed(): ?float { return $this->speed; }
    public function setSpeed(?float $speed): self { $this->speed = $speed; return $this; }

    public function getCourse(): ?float { return $this->course; }
    public function setCourse(?float $course): self { $this->course = $course; return $this; }

    public function getStreet(): ?string { return $this->street; }
    public function setStreet(?string $street): self { $this->street = $street; return $this; }

    public function getPostalcode(): ?string { return $this->postalcode; }
    public function setPostalcode(?string $postalcode): self { $this->postalcode = $postalcode; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): self { $this->city = $city; return $this; }

    public function getDisplay(): ?string { return $this->display; }
    public function setDisplay(?string $display): self { $this->display = $display; return $this; }

    public function getKmCounter(): string { return $this->kmCounter; }
    public function setKmCounter(string|float|int|null $kmCounter): self {
        if ($kmCounter === null || $kmCounter === '' || $kmCounter === 'unknown') {
            $this->kmCounter = 'unknown';
        } else {
            $this->kmCounter = (string)$kmCounter;
        }
        return $this;
    }

    public function getVehicle(): ?Vehicle { return $this->vehicle; }
    public function setVehicle(?Vehicle $vehicle): self { $this->vehicle = $vehicle; return $this; }

    public function getDriverId(): ?string { return $this->driverId; }
    public function setDriverId(?string $driverId): self { $this->driverId = $driverId; return $this; }
}
