<?php

namespace App\Entity;

use App\Repository\TrafficTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrafficTypeRepository::class)]
class TrafficType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Klarer, kurzer Name der Verkehrsart (z.B. Werkverkehr)
    #[ORM\Column(type: "string", length: 80)]
    private string $name;

    // Ausführliche Beschreibung inkl. Beispiele
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    // Relevante Rechtsgrundlage(n), z.B. "PBefG §43", "GüKG"
    #[ORM\Column(type: "string", length: 120, nullable: true)]
    private ?string $regulation = null;

    // Kategorie: Bus, LKW oder Beides (als ENUM/STRING, je nach Vorliebe)
    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private ?string $category = null; // "bus", "truck", "both"


    // Spezialinfos/Hinweise/Sonderregelungen
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $specialNotes = null;

    // Sortierreihenfolge
    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $sortOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
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

    public function getRegulation(): ?string
    {
        return $this->regulation;
    }

    public function setRegulation(?string $regulation): static
    {
        $this->regulation = $regulation;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getSpecialNotes(): ?string
    {
        return $this->specialNotes;
    }

    public function setSpecialNotes(?string $specialNotes): static
    {
        $this->specialNotes = $specialNotes;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
}
