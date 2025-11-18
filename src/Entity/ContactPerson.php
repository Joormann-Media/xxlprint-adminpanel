<?php

namespace App\Entity;

use App\Repository\ContactPersonRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Employee;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: ContactPersonRepository::class)]
class ContactPerson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    private ?string $salutation = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $streetNumber = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $zip = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $fax = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $web = null;

    // Wenn du willst: unique = false lassen (Mehrfachverwendung möglich)
    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'contactPersons')]
    private ?School $school = null;

    #[ORM\ManyToOne(targetEntity: Auftraggeber::class, inversedBy: 'contactPersons')]
    private ?Auftraggeber $auftraggeber = null;

    #[ORM\ManyToOne(targetEntity: Schoolkids::class, inversedBy: 'contactPersons')]
    private ?Schoolkids $schoolkid = null;
    #[ORM\Column(type: "string", length:50, nullable: true)]
    private ?string $contactType = null;

    #[ORM\OneToMany(mappedBy: 'emergencyContact', targetEntity: Employee::class)]
    private Collection $emergencyForEmployees;

    #[ORM\OneToMany(mappedBy: 'contactPerson', targetEntity: Schoolkids::class, cascade: ['persist', 'remove'])]
    private Collection $schoolkids;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;
    
#[ORM\ManyToOne(targetEntity: OfficialAddress::class)]
private ?OfficialAddress $address = null;
    

    // ----- Getter & Setter -----
    public function getId(): ?int { return $this->id; }
    public function getSalutation(): ?string { return $this->salutation; }
    public function setSalutation(?string $salutation): static { $this->salutation = $salutation; return $this; }
    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $firstName): static { $this->firstName = $firstName; return $this; }
    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(?string $lastName): static { $this->lastName = $lastName; return $this; }
    public function getRole(): ?string { return $this->role; }
    public function setRole(?string $role): static { $this->role = $role; return $this; }
    public function getStreet(): ?string { return $this->street; }
    public function setStreet(?string $street): static { $this->street = $street; return $this; }
    public function getStreetNumber(): ?string { return $this->streetNumber; }
    public function setStreetNumber(?string $streetNumber): static { $this->streetNumber = $streetNumber; return $this; }
    public function getZip(): ?string { return $this->zip; }
    public function setZip(?string $zip): static { $this->zip = $zip; return $this; }
    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): static { $this->city = $city; return $this; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): static { $this->phone = $phone; return $this; }
    public function getFax(): ?string { return $this->fax; }
    public function setFax(?string $fax): static { $this->fax = $fax; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): static { $this->email = $email; return $this; }
    public function getWeb(): ?string { return $this->web; }
    public function setWeb(?string $web): static { $this->web = $web; return $this; }
    public function getSchool(): ?School { return $this->school; }
    public function setSchool(?School $school): static { $this->school = $school; return $this; }
    public function getAuftraggeber(): ?Auftraggeber { return $this->auftraggeber; }
    public function setAuftraggeber(?Auftraggeber $auftraggeber): static { $this->auftraggeber = $auftraggeber; return $this; }
    public function getSchoolkid(): ?Schoolkids { return $this->schoolkid; }
    public function setSchoolkid(?Schoolkids $schoolkid): static { $this->schoolkid = $schoolkid; return $this; }
    // Zusatz: Vollständiger Name
    public function getFullName(): string
    {
        $parts = [];
        if ($this->salutation) $parts[] = $this->salutation;
        if ($this->firstName)  $parts[] = $this->firstName;
        if ($this->lastName)   $parts[] = $this->lastName;
        return trim(implode(' ', $parts));
    }

    public function __toString(): string
    {
        return $this->getFullName() .
            ($this->role ? ' ('.$this->role.')' : '') .
            ($this->phone ? ' • '.$this->phone : '');
    }

    public function getContactType(): ?string
{
    return $this->contactType;
}

public function setContactType(?string $contactType): static
{
    $this->contactType = $contactType;
    return $this;
}
public function __construct()
{
    $this->schoolkids = new ArrayCollection();
    $this->emergencyForEmployees = new ArrayCollection(); // falls noch nicht vorhanden
}


public function getEmergencyForEmployees(): Collection
{
    return $this->emergencyForEmployees;
}
/**
 * @return Collection<int, Schoolkids>
 */
public function getSchoolkids(): Collection
{
    return $this->schoolkids;
}

public function addSchoolkid(Schoolkids $schoolkid): static
{
    if (!$this->schoolkids->contains($schoolkid)) {
        $this->schoolkids->add($schoolkid);
        $schoolkid->setContactPerson($this); // ⬅ brauchst du gleich noch!
    }

    return $this;
}

public function removeSchoolkid(Schoolkids $schoolkid): static
{
    if ($this->schoolkids->removeElement($schoolkid)) {
        if ($schoolkid->getContactPerson() === $this) {
            $schoolkid->setContactPerson(null);
        }
    }

    return $this;
}

public function getUser(): ?User
{
    return $this->user;
}

public function setUser(?User $user): static
{
    $this->user = $user;
    return $this;
}

public function addEmergencyForEmployee(Employee $emergencyForEmployee): static
{
    if (!$this->emergencyForEmployees->contains($emergencyForEmployee)) {
        $this->emergencyForEmployees->add($emergencyForEmployee);
        $emergencyForEmployee->setEmergencyContact($this);
    }

    return $this;
}

public function removeEmergencyForEmployee(Employee $emergencyForEmployee): static
{
    if ($this->emergencyForEmployees->removeElement($emergencyForEmployee)) {
        // set the owning side to null (unless already changed)
        if ($emergencyForEmployee->getEmergencyContact() === $this) {
            $emergencyForEmployee->setEmergencyContact(null);
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

}
