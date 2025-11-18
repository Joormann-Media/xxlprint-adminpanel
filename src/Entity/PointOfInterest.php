<?php

namespace App\Entity;

use App\Repository\PointOfInterestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointOfInterestRepository::class)]
class PointOfInterest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // 🏷️ Name des Ortes
    #[ORM\Column(length: 255)]
    private string $name;

    // 📝 Beschreibung oder Zusatzinfos
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // 📍 Adresse
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    // 🌍 Geo-Koordinaten
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    // 📌 Typ (z. B. „Sammelpunkt“, „Haltestelle“, „Schule“)
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type = null;

    // --- Getter / Setter ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }
}
