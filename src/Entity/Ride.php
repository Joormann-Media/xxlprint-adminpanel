<?php

namespace App\Entity;

use App\Repository\RideRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RideRepository::class)]
class Ride
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Internal Ride ID (string, can be generated)
    #[ORM\Column(length: 50)]
    private ?string $rideId = null;

    // Reference to Client (Auftraggeber)
    #[ORM\ManyToOne(targetEntity: Auftraggeber::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Auftraggeber $client = null;

    #[ORM\Column(length: 255)]
    private ?string $startName = null;
    
    // Start address fields
    #[ORM\Column(length: 255)]
    private ?string $startStreet = null;

    #[ORM\Column(length: 20)]
    private ?string $startStreetNo = null;

    #[ORM\Column(length: 10)]
    private ?string $startZip = null;

    #[ORM\Column(length: 100)]
    private ?string $startCity = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $startCountry = null;

    // Destination address fields
    #[ORM\Column(length: 255)]
    private ?string $destStreet = null;

    #[ORM\Column(length: 20)]
    private ?string $destStreetNo = null;

    #[ORM\Column(length: 10)]
    private ?string $destZip = null;

    #[ORM\Column(length: 100)]
    private ?string $destCity = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $destCountry = null;

    // Ride variation (relation)
    #[ORM\ManyToOne(targetEntity: RideVariation::class)]
    private ?RideVariation $rideVariation = null;

    // Ride description
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $rideDescription = null;

    // Date/time fields
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $rideDateStart = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $rideDateEnd = null;

    // Optional: only time for start (if needed separately)
    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $rideTime = null;

    // Ride duration (as string, e.g. "45 min", or in minutes)
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $rideLength = null;

    // --- Getter & Setter ---

    public function getId(): ?int { return $this->id; }

    public function getRideId(): ?string { return $this->rideId; }
    public function setRideId(string $rideId): static { $this->rideId = $rideId; return $this; }

    public function getClient(): ?Auftraggeber { return $this->client; }
    public function setClient(?Auftraggeber $client): static { $this->client = $client; return $this; }

    public function getStartStreet(): ?string { return $this->startStreet; }
    public function setStartStreet(string $startStreet): static { $this->startStreet = $startStreet; return $this; }

    public function getStartStreetNo(): ?string { return $this->startStreetNo; }
    public function setStartStreetNo(string $startStreetNo): static { $this->startStreetNo = $startStreetNo; return $this; }

    public function getStartZip(): ?string { return $this->startZip; }
    public function setStartZip(string $startZip): static { $this->startZip = $startZip; return $this; }

    public function getStartCity(): ?string { return $this->startCity; }
    public function setStartCity(string $startCity): static { $this->startCity = $startCity; return $this; }

    public function getStartCountry(): ?string { return $this->startCountry; }
    public function setStartCountry(?string $startCountry): static { $this->startCountry = $startCountry; return $this; }

    public function getDestStreet(): ?string { return $this->destStreet; }
    public function setDestStreet(string $destStreet): static { $this->destStreet = $destStreet; return $this; }

    public function getDestStreetNo(): ?string { return $this->destStreetNo; }
    public function setDestStreetNo(string $destStreetNo): static { $this->destStreetNo = $destStreetNo; return $this; }

    public function getDestZip(): ?string { return $this->destZip; }
    public function setDestZip(string $destZip): static { $this->destZip = $destZip; return $this; }

    public function getDestCity(): ?string { return $this->destCity; }
    public function setDestCity(string $destCity): static { $this->destCity = $destCity; return $this; }

    public function getDestCountry(): ?string { return $this->destCountry; }
    public function setDestCountry(?string $destCountry): static { $this->destCountry = $destCountry; return $this; }

    public function getRideVariation(): ?RideVariation { return $this->rideVariation; }
    public function setRideVariation(?RideVariation $rideVariation): static { $this->rideVariation = $rideVariation; return $this; }

    public function getRideDescription(): ?string { return $this->rideDescription; }
    public function setRideDescription(?string $rideDescription): static { $this->rideDescription = $rideDescription; return $this; }

    public function getRideDateStart(): ?\DateTimeInterface { return $this->rideDateStart; }
    public function setRideDateStart(\DateTimeInterface $rideDateStart): static { $this->rideDateStart = $rideDateStart; return $this; }

    public function getRideDateEnd(): ?\DateTimeInterface { return $this->rideDateEnd; }
    public function setRideDateEnd(?\DateTimeInterface $rideDateEnd): static { $this->rideDateEnd = $rideDateEnd; return $this; }

    public function getRideTime(): ?\DateTimeInterface { return $this->rideTime; }
    public function setRideTime(?\DateTimeInterface $rideTime): static { $this->rideTime = $rideTime; return $this; }

    public function getRideLength(): ?string { return $this->rideLength; }
    public function setRideLength(?string $rideLength): static { $this->rideLength = $rideLength; return $this; }

    public function getStartName(): ?string
    {
        return $this->startName;
    }

    public function setStartName(string $startName): static
    {
        $this->startName = $startName;

        return $this;
    }
}
