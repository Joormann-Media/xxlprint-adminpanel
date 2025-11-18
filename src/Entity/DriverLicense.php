<?php

namespace App\Entity;

use App\Repository\DriverLicenseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverLicenseRepository::class)]
class DriverLicense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // 1. Persönliche Daten
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $lastName = null;          // Nachname (1.)
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $firstName = null;         // Vorname (2.)
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $birthPlace = null;        // Geburtsort (3.)
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $birthDate = null; // Geburtsdatum (3.)

    // 4a. Ausstellungsdatum
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $issuedAt = null;

    // 4b. Ablaufdatum
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $expiresAt = null;

    // 4c. Ausstellende Behörde
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $authority = null;

    // 5. Führerscheinnummer
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $licenseNumber = null;

    // 7. Unterschrift: als Bild/Scan oder String-Pfad
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $signatureImage = null;

    // 8. Wohnort (optional)
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $address = null;

    // 9. Klassen (jede als eigenes Feld, True/False zum Anhaken)
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classAM = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classA1 = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classA2 = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classA = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classB = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classBE = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classC1 = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classC1E = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classC = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classCE = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classD1 = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classD1E = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classD = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classDE = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classL = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classM = false;
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $classT = false;

    // Ggf. weitere Zusatzinfos, Auflagen, Bemerkungen
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $remarks = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // ... GETTER & SETTER hier (wie gehabt, auto-generieren lassen) ...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getBirthPlace(): ?string
    {
        return $this->birthPlace;
    }

    public function setBirthPlace(?string $birthPlace): static
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTime $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getIssuedAt(): ?\DateTime
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(?\DateTime $issuedAt): static
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTime $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getAuthority(): ?string
    {
        return $this->authority;
    }

    public function setAuthority(?string $authority): static
    {
        $this->authority = $authority;

        return $this;
    }

    public function getLicenseNumber(): ?string
    {
        return $this->licenseNumber;
    }

    public function setLicenseNumber(?string $licenseNumber): static
    {
        $this->licenseNumber = $licenseNumber;

        return $this;
    }

    public function getSignatureImage(): ?string
    {
        return $this->signatureImage;
    }

    public function setSignatureImage(?string $signatureImage): static
    {
        $this->signatureImage = $signatureImage;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function isClassAM(): ?bool
    {
        return $this->classAM;
    }

    public function setClassAM(bool $classAM): static
    {
        $this->classAM = $classAM;

        return $this;
    }

    public function isClassA1(): ?bool
    {
        return $this->classA1;
    }

    public function setClassA1(bool $classA1): static
    {
        $this->classA1 = $classA1;

        return $this;
    }

    public function isClassA2(): ?bool
    {
        return $this->classA2;
    }

    public function setClassA2(bool $classA2): static
    {
        $this->classA2 = $classA2;

        return $this;
    }

    public function isClassA(): ?bool
    {
        return $this->classA;
    }

    public function setClassA(bool $classA): static
    {
        $this->classA = $classA;

        return $this;
    }

    public function isClassB(): ?bool
    {
        return $this->classB;
    }

    public function setClassB(bool $classB): static
    {
        $this->classB = $classB;

        return $this;
    }

    public function isClassBE(): ?bool
    {
        return $this->classBE;
    }

    public function setClassBE(bool $classBE): static
    {
        $this->classBE = $classBE;

        return $this;
    }

    public function isClassC1(): ?bool
    {
        return $this->classC1;
    }

    public function setClassC1(bool $classC1): static
    {
        $this->classC1 = $classC1;

        return $this;
    }

    public function isClassC1E(): ?bool
    {
        return $this->classC1E;
    }

    public function setClassC1E(bool $classC1E): static
    {
        $this->classC1E = $classC1E;

        return $this;
    }

    public function isClassC(): ?bool
    {
        return $this->classC;
    }

    public function setClassC(bool $classC): static
    {
        $this->classC = $classC;

        return $this;
    }

    public function isClassCE(): ?bool
    {
        return $this->classCE;
    }

    public function setClassCE(bool $classCE): static
    {
        $this->classCE = $classCE;

        return $this;
    }

    public function isClassD1(): ?bool
    {
        return $this->classD1;
    }

    public function setClassD1(bool $classD1): static
    {
        $this->classD1 = $classD1;

        return $this;
    }

    public function isClassD1E(): ?bool
    {
        return $this->classD1E;
    }

    public function setClassD1E(bool $classD1E): static
    {
        $this->classD1E = $classD1E;

        return $this;
    }

    public function isClassD(): ?bool
    {
        return $this->classD;
    }

    public function setClassD(bool $classD): static
    {
        $this->classD = $classD;

        return $this;
    }

    public function isClassDE(): ?bool
    {
        return $this->classDE;
    }

    public function setClassDE(bool $classDE): static
    {
        $this->classDE = $classDE;

        return $this;
    }

    public function isClassL(): ?bool
    {
        return $this->classL;
    }

    public function setClassL(bool $classL): static
    {
        $this->classL = $classL;

        return $this;
    }

    public function isClassM(): ?bool
    {
        return $this->classM;
    }

    public function setClassM(bool $classM): static
    {
        $this->classM = $classM;

        return $this;
    }

    public function isClassT(): ?bool
    {
        return $this->classT;
    }

    public function setClassT(bool $classT): static
    {
        $this->classT = $classT;

        return $this;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function setRemarks(?string $remarks): static
    {
        $this->remarks = $remarks;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

}
