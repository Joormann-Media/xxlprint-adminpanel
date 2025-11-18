<?php

namespace App\Entity;

use App\Repository\DriverManagerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverManagerRepository::class)]
class DriverManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Verknüpfung zum User (jeder User kann genau einen DriverManager haben)
    #[ORM\OneToOne(inversedBy: 'driverManager', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Führerscheinklassen (Mehrere möglich!)
     */
    #[ORM\ManyToMany(targetEntity: LicenceClass::class)]
    private Collection $licenceClasses;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $licenceExpires = null;

    // Ein bekannter Fahrzeugtyp (kann leer sein)
    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    private ?Vehicle $knownVehicle = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $specialDriverInfo = null;

    public function __construct()
    {
        $this->licenceClasses = new ArrayCollection();
    }

    // --- Getter & Setter ---
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection<int, LicenceClass>
     */
    public function getLicenceClasses(): Collection
    {
        return $this->licenceClasses;
    }

    public function addLicenceClass(LicenceClass $licenceClass): self
    {
        if (!$this->licenceClasses->contains($licenceClass)) {
            $this->licenceClasses[] = $licenceClass;
        }
        return $this;
    }

    public function removeLicenceClass(LicenceClass $licenceClass): self
    {
        $this->licenceClasses->removeElement($licenceClass);
        return $this;
    }

    public function getLicenceExpires(): ?\DateTimeInterface
    {
        return $this->licenceExpires;
    }

    public function setLicenceExpires(?\DateTimeInterface $licenceExpires): self
    {
        $this->licenceExpires = $licenceExpires;
        return $this;
    }

    public function getKnownVehicle(): ?Vehicle
    {
        return $this->knownVehicle;
    }

    public function setKnownVehicle(?Vehicle $knownVehicle): self
    {
        $this->knownVehicle = $knownVehicle;
        return $this;
    }

    public function getSpecialDriverInfo(): ?string
    {
        return $this->specialDriverInfo;
    }

    public function setSpecialDriverInfo(?string $specialDriverInfo): self
    {
        $this->specialDriverInfo = $specialDriverInfo;
        return $this;
    }
}
