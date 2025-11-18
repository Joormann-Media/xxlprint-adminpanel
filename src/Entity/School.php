<?php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
class School
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $streetNo = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $zip = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: ContactPerson::class, inversedBy: null)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?ContactPerson $contactPerson = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shorttag = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: SchoolTime::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $schoolTimes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $district = null;

    #[ORM\Column(type: 'text', nullable: true)]
private ?string $additionalInfo = null;

#[ORM\ManyToOne(targetEntity: OfficialAddress::class)]
private ?OfficialAddress $address = null;

    #[ORM\OneToMany(mappedBy: 'school', targetEntity: SchoolTour::class, cascade: ['persist', 'remove'])]
    private Collection $tours;


    public function __construct()
    {
        $this->schoolTimes = new ArrayCollection();
        $this->tours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;
        return $this;
    }

    public function getStreetNo(): ?string
    {
        return $this->streetNo;
    }

    public function setStreetNo(?string $streetNo): static
    {
        $this->streetNo = $streetNo;
        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): static
    {
        $this->zip = $zip;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
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

    public function getShorttag(): ?string
    {
        return $this->shorttag;
    }

    public function setShorttag(?string $shorttag): static
    {
        $this->shorttag = $shorttag;
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

    /**
     * @return Collection<int, SchoolTime>
     */
    public function getSchoolTimes(): Collection
    {
        return $this->schoolTimes;
    }

    public function addSchoolTime(SchoolTime $schoolTime): static
    {
        if (!$this->schoolTimes->contains($schoolTime)) {
            $this->schoolTimes[] = $schoolTime;
            $schoolTime->setSchool($this);
        }
        return $this;
    }

    public function removeSchoolTime(SchoolTime $schoolTime): static
    {
        if ($this->schoolTimes->removeElement($schoolTime)) {
            if ($schoolTime->getSchool() === $this) {
                $schoolTime->setSchool(null);
            }
        }
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
public function getAdditionalInfo(): ?string
{
    return $this->additionalInfo;
}

public function setAdditionalInfo(?string $additionalInfo): static
{
    $this->additionalInfo = $additionalInfo;
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
     * @return Collection<int, SchoolTour>
     */
    public function getTours(): Collection
    {
        return $this->tours;
    }

    public function addTour(SchoolTour $tour): static
    {
        if (!$this->tours->contains($tour)) {
            $this->tours->add($tour);
            $tour->setSchool($this);
        }
        return $this;
    }

    public function removeTour(SchoolTour $tour): static
    {
        if ($this->tours->removeElement($tour)) {
            if ($tour->getSchool() === $this) {
                $tour->setSchool(null);
            }
        }
        return $this;
    }
}
