<?php

namespace App\Entity;

use App\Repository\StateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StateRepository::class)]
class State
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

     #[ORM\Column(length: 2, unique: true)]
    private string $code; // z.B. "NW", "BY", "BE"

    #[ORM\Column(length: 100, unique: true)]
    private string $name; // "Nordrhein-Westfalen", "Bayern", ...

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;   // Mittelpunkt (optional)

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;  // Mittelpunkt (optional)

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $polygon = null; // Für Polygon-Koordinaten als GeoJSON-Array

    public function getId(): int { return $this->id; }

    public function getCode(): string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(?float $latitude): static { $this->latitude = $latitude; return $this; }

    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(?float $longitude): static { $this->longitude = $longitude; return $this; }

    public function getPolygon(): ?array { return $this->polygon; }
    public function setPolygon(?array $polygon): static { $this->polygon = $polygon; return $this; }
}