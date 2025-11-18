<?php

namespace App\Entity;

use App\Repository\SchoolkidsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: SchoolkidsRepository::class)]
class Schoolkids
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Connection to school (ManyToOne, one kid per school)
    #[ORM\ManyToOne(targetEntity: School::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column(length: 20)]
    private ?string $streetNumber = null;

    #[ORM\Column(length: 10)]
    private ?string $zip = null;

    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $district = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $contactPersonName = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $kidPhone = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $contactPersonPhone = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $specialInfos = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private ?bool $active = true;

    // --- NEW FIELDS ---

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private ?bool $needsAid = false; // Needs aid? (e.g. wheelchair, walker...)

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $aidType = null; // Type of aid (wheelchair, walker, etc.)

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private ?bool $hasCompanion = false; // Accompanied by a person?

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $companionName = null; // Name of companion

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $requiredSeats = 1; // Needed seats (e.g. 2 if with companion)

    // --- Optional: Mail, Geo, Date of Birth, etc. ---

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $contactEmail = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;
    #[ORM\OneToMany(mappedBy: 'schoolkid', targetEntity: ContactPerson::class, cascade: ['persist', 'remove'])]
private Collection $contactPersons;

#[ORM\ManyToOne(inversedBy: 'schoolkids')]
#[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
private ?ContactPerson $contactPerson = null;
#[ORM\OneToMany(mappedBy: 'schoolkid', targetEntity: SchoolkidAbsence::class, orphanRemoval: true, cascade: ['persist'])]
private Collection $absences;

#[ORM\ManyToOne(targetEntity: OfficialAddress::class)]
private ?OfficialAddress $address = null;

#[ORM\OneToMany(mappedBy: 'kid', targetEntity: KidsBlacklist::class, cascade: ['remove'])]
private Collection $blacklists;

#[ORM\OneToMany(mappedBy: 'enemy', targetEntity: KidsBlacklist::class, cascade: ['remove'])]
private Collection $blacklistedBy;

#[ORM\ManyToMany(targetEntity: SchoolTour::class, mappedBy: 'kids')]
private Collection $tours;

#[ORM\ManyToMany(targetEntity: SchoolTourStop::class, mappedBy: 'kids')]
private Collection $stops;


public function __construct()
{
    $this->contactPersons = new ArrayCollection();
    $this->absences = new ArrayCollection();
    $this->blacklists = new ArrayCollection();
    $this->blacklistedBy = new ArrayCollection();
    $this->tours = new ArrayCollection();
    $this->stops = new ArrayCollection();

    // ... ggf. andere Collections
}
/**
 * @return Collection<int, SchoolTourStop>
 */
public function getStops(): Collection { return $this->stops; }
    // ... weitere Felder nach Bedarf

    // --- Getter/Setter ... ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(string $streetNumber): static
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(string $zip): static
    {
        $this->zip = $zip;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): static
    {
        $this->district = $district;

        return $this;
    }

    public function getContactPersonName(): ?string
    {
        return $this->contactPersonName;
    }

    public function setContactPersonName(?string $contactPersonName): static
    {
        $this->contactPersonName = $contactPersonName;

        return $this;
    }

    public function getKidPhone(): ?string
    {
        return $this->kidPhone;
    }

    public function setKidPhone(?string $kidPhone): static
    {
        $this->kidPhone = $kidPhone;

        return $this;
    }

    public function getContactPersonPhone(): ?string
    {
        return $this->contactPersonPhone;
    }

    public function setContactPersonPhone(?string $contactPersonPhone): static
    {
        $this->contactPersonPhone = $contactPersonPhone;

        return $this;
    }

    public function getSpecialInfos(): ?string
    {
        return $this->specialInfos;
    }

    public function setSpecialInfos(?string $specialInfos): static
    {
        $this->specialInfos = $specialInfos;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function isNeedsAid(): ?bool
    {
        return $this->needsAid;
    }

    public function setNeedsAid(bool $needsAid): static
    {
        $this->needsAid = $needsAid;

        return $this;
    }

    public function getAidType(): ?string
    {
        return $this->aidType;
    }

    public function setAidType(?string $aidType): static
    {
        $this->aidType = $aidType;

        return $this;
    }

    public function hasCompanion(): ?bool
    {
        return $this->hasCompanion;
    }

    public function setHasCompanion(bool $hasCompanion): static
    {
        $this->hasCompanion = $hasCompanion;

        return $this;
    }

    public function getCompanionName(): ?string
    {
        return $this->companionName;
    }

    public function setCompanionName(?string $companionName): static
    {
        $this->companionName = $companionName;

        return $this;
    }

    public function getRequiredSeats(): ?int
    {
        return $this->requiredSeats;
    }

    public function setRequiredSeats(?int $requiredSeats): static
    {
        $this->requiredSeats = $requiredSeats;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

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

    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTime $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

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

    public function getContactPersons(): Collection
{
    return $this->contactPersons;
}

public function addContactPerson(ContactPerson $contactPerson): static
{
    if (!$this->contactPersons->contains($contactPerson)) {
        $this->contactPersons[] = $contactPerson;
        $contactPerson->setSchoolkid($this);
    }
    return $this;
}

public function removeContactPerson(ContactPerson $contactPerson): static
{
    if ($this->contactPersons->removeElement($contactPerson)) {
        if ($contactPerson->getSchoolkid() === $this) {
            $contactPerson->setSchoolkid(null);
        }
    }
    return $this;
}
public function getContactPerson(): ?ContactPerson
{
    return $this->contactPerson;
}

public function setContactPerson(?ContactPerson $contactPerson): static
{
    $this->contactPerson = $contactPerson;

    return $this;
}

/**
 * @return Collection<int, SchoolkidAbsence>
 */
public function getAbsences(): Collection
{
    return $this->absences;
}

public function addAbsence(SchoolkidAbsence $absence): static
{
    if (!$this->absences->contains($absence)) {
        $this->absences->add($absence);
        $absence->setSchoolkid($this);
    }

    return $this;
}

public function removeAbsence(SchoolkidAbsence $absence): static
{
    if ($this->absences->removeElement($absence)) {
        // set the owning side to null (unless already changed)
        if ($absence->getSchoolkid() === $this) {
            $absence->setSchoolkid(null);
        }
    }

    return $this;
}

public function getAddress(): ?OfficialAddress
{
    return $this->address;
}

public function setAddress(?OfficialAddress $address): static
{
    $this->address = $address;

    return $this;
}
/**
 * Kinder, die von diesem Kind nicht gemocht werden.
 * @return Collection<int, KidsBlacklist>
 */
public function getBlacklists(): Collection
{
    return $this->blacklists;
}

/**
 * Kinder, die dieses Kind nicht mögen.
 * @return Collection<int, KidsBlacklist>
 */
public function getBlacklistedBy(): Collection
{
    return $this->blacklistedBy;
}
public function getTours(): Collection
{
    return $this->tours;
}

public function addTour(SchoolTour $tour): static
{
    if (!$this->tours->contains($tour)) {
        $this->tours->add($tour);
        $tour->addKid($this);
    }
    return $this;
}

public function removeTour(SchoolTour $tour): static
{
    if ($this->tours->removeElement($tour)) {
        $tour->removeKid($this);
    }
    return $this;
}

}
