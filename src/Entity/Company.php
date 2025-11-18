<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CostCenter;


#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyname = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ceoname = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ceoprename = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $streetno = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $zipcode = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fax = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $web = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $taxno = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $taxid = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyLogo = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePath = '';

    #[ORM\Column(type: 'integer', nullable: true)]
private ?int $personalNrMin = null;

#[ORM\Column(type: 'integer', nullable: true)]
private ?int $personalNrMax = null;

#[ORM\OneToMany(mappedBy: "company", targetEntity: Employee::class)]
private $employees;
// In der Company-Entity:
#[ORM\OneToMany(mappedBy: 'company', targetEntity: CostCenter::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
private Collection $costCenters;


public function __construct()
{
    $this->employees = new ArrayCollection();
   $this->costCenters = new ArrayCollection();
}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyname(): ?string
    {
        return $this->companyname;
    }

    public function setCompanyname(string $companyname): static
    {
        $this->companyname = $companyname ?? '';

        return $this;
    }

    public function getCeoname(): ?string
    {
        return $this->ceoname;
    }

    public function setCeoname(string $ceoname): static
    {
        $this->ceoname = $ceoname ?? '';

        return $this;
    }

    public function getCeoprename(): ?string
    {
        return $this->ceoprename;
    }

    public function setCeoprename(string $ceoprename): static
    {
        $this->ceoprename = $ceoprename ?? '';

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street ?? '';

        return $this;
    }

    public function getStreetno(): ?string
    {
        return $this->streetno;
    }

    public function setStreetno(string $streetno): static
    {
        $this->streetno = $streetno ?? '';

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): static
    {
        $this->zipcode = $zipcode ?? '';

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city ?? '';

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location ?? '';

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone ?? '';

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(string $fax): static
    {
        $this->fax = $fax ?? '';

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email ?? '';

        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(string $web): static
    {
        $this->web = $web ?? '';

        return $this;
    }

    public function getTaxno(): ?string
    {
        return $this->taxno;
    }

    public function setTaxno(string $taxno): static
    {
        $this->taxno = $taxno ?? '';

        return $this;
    }

    public function getTaxid(): ?string
    {
        return $this->taxid;
    }

    public function setTaxid(string $taxid): static
    {
        $this->taxid = $taxid ?? '';

        return $this;
    }

    public function getCompanyLogo(): ?string
    {
        return $this->companyLogo;
    }

    public function setCompanyLogo(string $companyLogo): static
    {
        $this->companyLogo = $companyLogo ?? '';

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): static
    {
        $this->imagePath = $imagePath ?? '';

        return $this;
    }
    public function getPersonalNrMin(): ?int
{
    return $this->personalNrMin;
}

public function setPersonalNrMin(?int $personalNrMin): static
{
    $this->personalNrMin = $personalNrMin;
    return $this;
}

public function getPersonalNrMax(): ?int
{
    return $this->personalNrMax;
}

public function setPersonalNrMax(?int $personalNrMax): static
{
    $this->personalNrMax = $personalNrMax;
    return $this;
}
/** @return Collection<int, CostCenter> */
public function getCostCenters(): Collection
{
    return $this->costCenters;
}

public function addCostCenter(CostCenter $costCenter): static
{
    if (!$this->costCenters->contains($costCenter)) {
        $this->costCenters->add($costCenter);
        $costCenter->setCompany($this);
    }
    return $this;
}

public function removeCostCenter(CostCenter $costCenter): static
{
    if ($this->costCenters->removeElement($costCenter)) {
        if ($costCenter->getCompany() === $this) {
            $costCenter->setCompany(null);
        }
    }
    return $this;
}
}
