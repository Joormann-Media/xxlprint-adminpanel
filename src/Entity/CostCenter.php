<?php

namespace App\Entity;

use App\Repository\CostCenterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Company;

#[ORM\Entity(repositoryClass: CostCenterRepository::class)]
class CostCenter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null; // z.B. "KT-1234", "LOHN01", "BUS01"

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null; // "Busfahrer Lohnkosten", "Zentrale", etc.

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    #[ORM\ManyToMany(targetEntity: Employee::class, inversedBy: 'costCenters')]
    private Collection $employees;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'costCenters')]
#[ORM\JoinColumn(nullable: false)]
private ?Company $company = null;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    // Getter und Setter...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): static
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): static
    {
        $this->employees->removeElement($employee);

        return $this;
    }

    // Getter und Setter
public function getCompany(): ?Company
{
    return $this->company;
}

public function setCompany(?Company $company): static
{
    $this->company = $company;
    return $this;
}
}