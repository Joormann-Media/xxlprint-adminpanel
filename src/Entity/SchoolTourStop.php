<?php

namespace App\Entity;

use App\Repository\SchoolTourStopRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


#[ORM\Entity(repositoryClass: SchoolTourStopRepository::class)]
class SchoolTourStop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // 🚍 Zugehörige Tour
    #[ORM\ManyToOne(inversedBy: 'stops')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SchoolTour $tour = null;

    // 👧 Kinder, die an diesem Stop ein-/aussteigen
    #[ORM\ManyToMany(targetEntity: Schoolkids::class, inversedBy: 'stops')]
    #[ORM\JoinTable(name: 'schooltourstop_kids')]
    private Collection $kids;

    // 🏫 Optional: Bezug zu einer Schule (z. B. Ziel)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?School $school = null;

    // 📍 Freie Adresse (z. B. Sammelpunkt)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    // 🗺️ Koordinaten
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    // 🕒 Geplante Uhrzeit
    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $plannedTime = null;

    // 🔢 Reihenfolge in der Tour
    #[ORM\Column(type: 'integer')]
    private int $sortOrder = 0;

    // 📝 Kommentar / Zusatzinfos
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;
    #[ORM\ManyToOne]

    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?StopPoint $stopPoint = null;

    // --- Getter/Setter ---
    public function __construct()
    {
        $this->kids = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTour(): ?SchoolTour { return $this->tour; }
    public function setTour(?SchoolTour $tour): static { $this->tour = $tour; return $this; }


    public function getSchool(): ?School { return $this->school; }
    public function setSchool(?School $school): static { $this->school = $school; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): static { $this->address = $address; return $this; }

    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(?float $latitude): static { $this->latitude = $latitude; return $this; }

    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(?float $longitude): static { $this->longitude = $longitude; return $this; }

    public function getPlannedTime(): ?\DateTimeInterface { return $this->plannedTime; }
    public function setPlannedTime(?\DateTimeInterface $plannedTime): static { $this->plannedTime = $plannedTime; return $this; }

    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $sortOrder): static { $this->sortOrder = $sortOrder; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): static { $this->notes = $notes; return $this; }

    public function getStopPoint(): ?StopPoint
    {
        return $this->stopPoint;
    }

    public function setStopPoint(?StopPoint $stopPoint): static
    {
        $this->stopPoint = $stopPoint;
        return $this;
    }
    /**
     * @return Collection<int, Schoolkids>
     */
    public function getKids(): Collection { return $this->kids; }

    public function addKid(Schoolkids $kid): static
    {
        if (!$this->kids->contains($kid)) {
            $this->kids->add($kid);
        }
        return $this;
    }

    public function removeKid(Schoolkids $kid): static
    {
        $this->kids->removeElement($kid);
        return $this;
    }
}
