<?php

namespace App\Entity;

use App\Repository\NFCScanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NFCScanRepository::class)]
class NFCScan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 32, unique: true)]
    private ?string $uid = null;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $atr = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $chipType = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $memory = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $rawInfo = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $scannedAt = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $manufacturer = null;

    // Entferne das alte "medium"-Feld!
    // #[ORM\Column(type: 'string', length: 64, nullable: true)]
    // private ?string $medium = null;

    // Neu: Kategorie (z. B. "NFC Karten")
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $mediumType = null;

    // Neu: Beschreibung/Variante (z. B. "NFC Karten transparent")
    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $mediumDescription = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $features = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $protocols = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $lockStatus = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isWritable = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $writeEndurance = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $writeCounter = null;

    // --- Getter/Setter ---

    public function getId(): ?int { return $this->id; }
    public function getUid(): ?string { return $this->uid; }
    public function setUid(string $uid): self { $this->uid = $uid; return $this; }
    public function getScannedAt(): ?\DateTimeImmutable { return $this->scannedAt; }
    public function setScannedAt(\DateTimeImmutable $scannedAt): self { $this->scannedAt = $scannedAt; return $this; }
    public function getAtr(): ?string { return $this->atr; }
    public function setAtr(?string $atr): static { $this->atr = $atr; return $this; }
    public function getChipType(): ?string { return $this->chipType; }
    public function setChipType(?string $chipType): static { $this->chipType = $chipType; return $this; }
    public function getMemory(): ?int { return $this->memory; }
    public function setMemory(?int $memory): static { $this->memory = $memory; return $this; }
    public function getRawInfo(): ?array { return $this->rawInfo; }
    public function setRawInfo(?array $rawInfo): static { $this->rawInfo = $rawInfo; return $this; }
    public function getManufacturer(): ?string { return $this->manufacturer; }
    public function setManufacturer(?string $manufacturer): static { $this->manufacturer = $manufacturer; return $this; }

    // --- NEU: MediumType ---
    public function getMediumType(): ?string { return $this->mediumType; }
    public function setMediumType(?string $mediumType): static { $this->mediumType = $mediumType; return $this; }

    // --- NEU: MediumDescription ---
    public function getMediumDescription(): ?string { return $this->mediumDescription; }
    public function setMediumDescription(?string $mediumDescription): static { $this->mediumDescription = $mediumDescription; return $this; }

    public function getFeatures(): ?array { return $this->features; }
    public function setFeatures(?array $features): static { $this->features = $features; return $this; }
    public function getProtocols(): ?string { return $this->protocols; }
    public function setProtocols(?string $protocols): static { $this->protocols = $protocols; return $this; }
    public function getLockStatus(): ?array { return $this->lockStatus; }
    public function setLockStatus(?array $lockStatus): static { $this->lockStatus = $lockStatus; return $this; }
    public function isWritable(): ?bool { return $this->isWritable; }
    public function setIsWritable(?bool $isWritable): static { $this->isWritable = $isWritable; return $this; }
    public function getWriteEndurance(): ?int { return $this->writeEndurance; }
    public function setWriteEndurance(?int $writeEndurance): static { $this->writeEndurance = $writeEndurance; return $this; }
    public function getWriteCounter(): ?int { return $this->writeCounter; }
    public function setWriteCounter(?int $writeCounter): static { $this->writeCounter = $writeCounter; return $this; }
}
