<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 20, nullable: true, unique: true)]
    private ?string $employeeNumber = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isDriver = true;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $shortCode = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $hiredAt = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $agreedHoursDaily = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $agreedHoursWeekly = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $agreedHoursMonthly = null;

    // --- RELATIONEN ---

    #[ORM\ManyToMany(targetEntity: LicenceClass::class)]
    #[ORM\JoinTable(name: 'employee_licence_classes')]
    private Collection $licenceClasses;

    #[ORM\OneToMany(mappedBy: 'driver', targetEntity: Vehicle::class)]
    private Collection $vehicles;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: ContactPerson::class, inversedBy: 'emergencyForEmployees')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ContactPerson $emergencyContact = null;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: EmployeeDocument::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $documents;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: "employees")]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Company $company = null;

    #[ORM\ManyToMany(targetEntity: CostCenter::class, mappedBy: 'employees')]
    private Collection $costCenters;

    #[ORM\ManyToOne(targetEntity: OfficialAddress::class)]
    private ?OfficialAddress $address = null;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: EmployeeVacation::class, cascade: ['persist', 'remove'])]
    private Collection $vacations;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: EmployeeAbsence::class)]
private Collection $absences;


    // --- KONSTRUKTOR ---
    public function __construct()
    {
        $this->licenceClasses = new ArrayCollection();
        $this->vehicles = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->costCenters = new ArrayCollection();
        $this->vacations = new ArrayCollection();
        $this->absences = new ArrayCollection();
    }

    // --- GETTER / SETTER ---

    public function getId(): ?int { return $this->id; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }

    public function getEmployeeNumber(): ?string { return $this->employeeNumber; }
    public function setEmployeeNumber(?string $employeeNumber): self { $this->employeeNumber = $employeeNumber; return $this; }

    public function isDriver(): bool { return $this->isDriver; }
    public function setIsDriver(bool $isDriver): self { $this->isDriver = $isDriver; return $this; }

    public function getBirthDate(): ?\DateTimeInterface { return $this->birthDate; }
    public function setBirthDate(?\DateTimeInterface $birthDate): self { $this->birthDate = $birthDate; return $this; }

    public function getShortCode(): ?string { return $this->shortCode; }
    public function setShortCode(?string $shortCode): self { $this->shortCode = $shortCode; return $this; }

    public function getHiredAt(): ?\DateTimeInterface { return $this->hiredAt; }
    public function setHiredAt(?\DateTimeInterface $hiredAt): self { $this->hiredAt = $hiredAt; return $this; }

    public function getAgreedHoursDaily(): ?float { return $this->agreedHoursDaily; }
    public function setAgreedHoursDaily(?float $agreedHoursDaily): static { $this->agreedHoursDaily = $agreedHoursDaily; return $this; }

    public function getAgreedHoursWeekly(): ?float { return $this->agreedHoursWeekly; }
    public function setAgreedHoursWeekly(?float $agreedHoursWeekly): static { $this->agreedHoursWeekly = $agreedHoursWeekly; return $this; }

    public function getAgreedHoursMonthly(): ?float { return $this->agreedHoursMonthly; }
    public function setAgreedHoursMonthly(?float $agreedHoursMonthly): static { $this->agreedHoursMonthly = $agreedHoursMonthly; return $this; }

    /**
     * @return Collection<int, LicenceClass>
     */
    public function getLicenceClasses(): Collection { return $this->licenceClasses; }

    public function addLicenceClass(LicenceClass $licenceClass): static
    {
        if (!$this->licenceClasses->contains($licenceClass)) {
            $this->licenceClasses->add($licenceClass);
        }
        return $this;
    }

    public function removeLicenceClass(LicenceClass $licenceClass): static
    {
        $this->licenceClasses->removeElement($licenceClass);
        return $this;
    }

    /**
     * @return Collection<int, Vehicle>
     */
    public function getVehicles(): Collection { return $this->vehicles; }

    public function addVehicle(Vehicle $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
            $vehicle->setDriver($this);
        }
        return $this;
    }

    public function removeVehicle(Vehicle $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)) {
            if ($vehicle->getDriver() === $this) {
                $vehicle->setDriver(null);
            }
        }
        return $this;
    }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getEmergencyContact(): ?ContactPerson { return $this->emergencyContact; }
    public function setEmergencyContact(?ContactPerson $emergencyContact): static { $this->emergencyContact = $emergencyContact; return $this; }

    /**
     * @return Collection<int, EmployeeDocument>
     */
    public function getDocuments(): Collection { return $this->documents; }

    public function addDocument(EmployeeDocument $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setEmployee($this);
        }
        return $this;
    }

    public function removeDocument(EmployeeDocument $document): static
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getEmployee() === $this) {
                $document->setEmployee(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, CostCenter>
     */
    public function getCostCenters(): Collection { return $this->costCenters; }

    public function addCostCenter(CostCenter $costCenter): static
    {
        if (!$this->costCenters->contains($costCenter)) {
            $this->costCenters->add($costCenter);
            $costCenter->addEmployee($this);
        }
        return $this;
    }

    public function removeCostCenter(CostCenter $costCenter): static
    {
        if ($this->costCenters->removeElement($costCenter)) {
            $costCenter->removeEmployee($this);
        }
        return $this;
    }

    public function getAddress(): ?OfficialAddress { return $this->address; }
    public function setAddress(?OfficialAddress $address): static { $this->address = $address; return $this; }

    /**
     * @return Collection<int, EmployeeVacation>
     */
    public function getVacations(): Collection { return $this->vacations; }

    public function addVacation(EmployeeVacation $vacation): static
    {
        if (!$this->vacations->contains($vacation)) {
            $this->vacations->add($vacation);
            $vacation->setEmployee($this);
        }
        return $this;
    }

    public function removeVacation(EmployeeVacation $vacation): static
    {
        if ($this->vacations->removeElement($vacation)) {
            if ($vacation->getEmployee() === $this) {
                $vacation->setEmployee(null);
            }
        }
        return $this;
    }

    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, EmployeeAbsence>
     */
    public function getAbsences(): Collection
    {
        return $this->absences;
    }

    public function addAbsence(EmployeeAbsence $absence): static
    {
        if (!$this->absences->contains($absence)) {
            $this->absences->add($absence);
            $absence->setEmployee($this);
        }

        return $this;
    }

    public function removeAbsence(EmployeeAbsence $absence): static
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getEmployee() === $this) {
                $absence->setEmployee(null);
            }
        }

        return $this;
    }
}
