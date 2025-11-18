<?php

namespace App\Entity;

use App\Repository\SchoolTourRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\SchoolTourStatus;
use App\Enum\CompanionRequirement;

#[ORM\Entity(repositoryClass: SchoolTourRepository::class)]
class SchoolTour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // 🏷️ Tourname
    #[ORM\Column(length: 100)]
    private string $name;

    // 📝 Beschreibung
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // 🔄 Typ (hin/rück)
    #[ORM\Column(length: 20)]
    private string $type; // Werte: "outbound" / "return"

    // 🏫 Schule
    #[ORM\ManyToOne(inversedBy: 'tours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    // 👧 Kinder (mehrere pro Tour)
    #[ORM\ManyToMany(targetEntity: Schoolkids::class, inversedBy: 'tours')]
    private Collection $kids;

    // 📅 Betriebstage (z. B. Mo-Fr)
    #[ORM\Column(type: 'json')]
    private array $operatingDays = []; // ["monday","tuesday","wednesday"]

    // 📘 Schuljahr
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $schoolYear = null;

    // 🏠 Startadresse + Uhrzeit
    #[ORM\Column(length: 255)]
    private string $startAddress = '';

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $startTime = null;

    // 🎯 Zieladresse + Uhrzeit
    #[ORM\Column(length: 255)]
    private string $endAddress = '';

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $endTime = null;

    // 🛣️ Route (kann z. B. als GeoJSON gespeichert werden)
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $route = null;

    #[ORM\OneToMany(mappedBy: 'tour', targetEntity: SchoolTourStop::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    private Collection $stops;

    // 👤 Ersteller / Maintainer
#[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: true)]
private ?User $maintainer = null;

    // 👤 Genehmiger
#[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: true)]
private ?User $approvedBy = null;

    // 📅 Genehmigungsdatum
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approvedDate = null;

    // 📅 Erstellungsdatum
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $tourCreated;

    // 🔖 Status
    #[ORM\Column(type: 'string', enumType: SchoolTourStatus::class, nullable: false)]
    private SchoolTourStatus $status;


        // 🛣️ Distanz in Kilometern
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $distance = null;

    // ⏱️ Dauer in Sekunden
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $duration = null;
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $startLatitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $startLongitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $endLatitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $endLongitude = null;

    // 📌 Begleiter (nicht erforderlich, optional, erforderlich)
    #[ORM\Column(type: 'string', enumType: CompanionRequirement::class, nullable: true)]
    private ?CompanionRequirement $companionRequirement = CompanionRequirement::NOT_REQUIRED;

    public function __construct()
    {
        $this->kids = new ArrayCollection();
        $this->stops = new ArrayCollection();
        $this->tourCreated = new \DateTime(); // automatisch beim Anlegen
        $this->status = SchoolTourStatus::PENDING_WIP; // ✅ Default
        $this->companionRequirement = CompanionRequirement::NOT_REQUIRED; // ✅ Default
    }

    // TODO: Getter/Setter

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getOperatingDays(): array
    {
        return $this->operatingDays;
    }

    public function setOperatingDays(array $operatingDays): static
    {
        $this->operatingDays = $operatingDays;

        return $this;
    }

    public function getSchoolYear(): ?string
    {
        return $this->schoolYear;
    }

    public function setSchoolYear(?string $schoolYear): static
    {
        $this->schoolYear = $schoolYear;

        return $this;
    }

    public function getStartAddress(): ?string
    {
        return $this->startAddress;
    }

    public function setStartAddress(string $startAddress): static
    {
        $this->startAddress = $startAddress;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndAddress(): ?string
    {
        return $this->endAddress;
    }

    public function setEndAddress(string $endAddress): static
    {
        $this->endAddress = $endAddress;

        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): static
    {
        $this->endTime = $endTime;

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

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): static
    {
        $this->school = $school;

        return $this;
    }

    /**
     * @return Collection<int, Schoolkids>
     */
    public function getKids(): Collection
    {
        return $this->kids;
    }

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
    /**
 * @return Collection<int, SchoolTourStop>
 */
public function getStops(): Collection { return $this->stops; }

public function addStop(SchoolTourStop $stop): static
{
    if (!$this->stops->contains($stop)) {
        $this->stops->add($stop);
        $stop->setTour($this);
    }
    return $this;
}

public function removeStop(SchoolTourStop $stop): static
{
    if ($this->stops->removeElement($stop)) {
        if ($stop->getTour() === $this) {
            $stop->setTour(null);
        }
    }
    return $this;
}

 public function getMaintainer(): ?User
                                  {
                                      return $this->maintainer;
                                  }

    public function setMaintainer(?User $maintainer): static
    {
        $this->maintainer = $maintainer;
        return $this;
    }

    public function getApprovedBy(): ?User
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?User $approvedBy): static
    {
        $this->approvedBy = $approvedBy;
        return $this;
    }

    public function getApprovedDate(): ?\DateTimeInterface
    {
        return $this->approvedDate;
    }

    public function setApprovedDate(?\DateTimeInterface $approvedDate): static
    {
        $this->approvedDate = $approvedDate;
        return $this;
    }

    public function getTourCreated(): \DateTimeInterface
    {
        return $this->tourCreated;
    }

    public function setTourCreated(\DateTimeInterface $tourCreated): static
    {
        $this->tourCreated = $tourCreated;
        return $this;
    }

public function getStatus(): SchoolTourStatus
{
    return $this->status;
}

public function setStatus(SchoolTourStatus $status): static
{
    $this->status = $status;
    return $this;
}
    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(?float $distance): static
    {
        $this->distance = $distance;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;
        return $this;
    }

    public function getStartLatitude(): ?float
    {
        return $this->startLatitude;
    }

    public function setStartLatitude(?float $startLatitude): static
    {
        $this->startLatitude = $startLatitude;

        return $this;
    }

    public function getStartLongitude(): ?float
    {
        return $this->startLongitude;
    }

    public function setStartLongitude(?float $startLongitude): static
    {
        $this->startLongitude = $startLongitude;

        return $this;
    }

    public function getEndLatitude(): ?float
    {
        return $this->endLatitude;
    }

    public function setEndLatitude(?float $endLatitude): static
    {
        $this->endLatitude = $endLatitude;

        return $this;
    }

    public function getEndLongitude(): ?float
    {
        return $this->endLongitude;
    }

    public function setEndLongitude(?float $endLongitude): static
    {
        $this->endLongitude = $endLongitude;

        return $this;
    }

    public function getCompanionRequirement(): ?CompanionRequirement
    {
        return $this->companionRequirement;
    }

    public function setCompanionRequirement(CompanionRequirement $companionRequirement): static
    {
        $this->companionRequirement = $companionRequirement;

        return $this;
    }

}
