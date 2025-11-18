<?php

namespace App\Entity;

use App\Repository\StopPointRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StopPointRepository::class)]
class StopPoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null; // Freitext, z.B. "Kita Regenbogen", "Bushaltestelle Bahnhof"

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $streetNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $crossingStreet = null; // Für Kreuzungen

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $zip = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $type = null; 
    // Mögliche Werte: "address", "crossing", "bus_stop", "bus_parking"
    // -> In der Form als ChoiceField, im Backend als ENUM oder einfacher String

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $maxPersons = null; // Wie viele können einsammeln

    // Optional: Bemerkungstext (z.B. Hinweise)
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stopPointIcon = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mapViewport = null;



    // Getter & Setter (nur exemplarisch)
    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): static { $this->name = $name; return $this; }

    public function getStreet(): ?string { return $this->street; }
    public function setStreet(?string $street): static { $this->street = $street; return $this; }

    public function getStreetNumber(): ?string { return $this->streetNumber; }
    public function setStreetNumber(?string $streetNumber): static { $this->streetNumber = $streetNumber; return $this; }

    public function getCrossingStreet(): ?string { return $this->crossingStreet; }
    public function setCrossingStreet(?string $crossingStreet): static { $this->crossingStreet = $crossingStreet; return $this; }

    public function getZip(): ?string { return $this->zip; }
    public function setZip(?string $zip): static { $this->zip = $zip; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): static { $this->city = $city; return $this; }

    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(?float $latitude): static { $this->latitude = $latitude; return $this; }

    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(?float $longitude): static { $this->longitude = $longitude; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(?string $type): static { $this->type = $type; return $this; }

    public function getMaxPersons(): ?int { return $this->maxPersons; }
    public function setMaxPersons(?int $maxPersons): static { $this->maxPersons = $maxPersons; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): static { $this->notes = $notes; return $this; }

    public function getStopPointIcon(): ?string
{
    return $this->stopPointIcon;
}

public function setStopPointIcon(?string $stopPointIcon): static
{
    $this->stopPointIcon = $stopPointIcon;
    return $this;
}
public function getMapViewport(): ?string
{
    return $this->mapViewport;
}

public function setMapViewport(?string $mapViewport): static
{
    $this->mapViewport = $mapViewport;
    return $this;
}

}
