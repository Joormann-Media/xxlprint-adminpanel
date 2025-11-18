<?php

namespace App\Entity;

use App\Repository\NfcStickerCardRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NfcStickerCardRepository::class)]
class NfcStickerCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // UID ist einzigartig
    #[ORM\Column(length: 32, unique: true)]
    #[Assert\NotBlank]
    private ?string $uid = null;

    // Typ: Sticker oder Karte
    #[ORM\Column(length: 16)]
    private ?string $type = null; // 'Sticker', 'Karte', 'Sonstiges', ...

    // Label, z.B. für beschriftete Sticker/Karten
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $label = null;

    // Ausgabe-/Einsatzzweck
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $purpose = null;

    // Zugeordneter User (nullable)
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    // Zugeordnetes Fahrzeug/Objekt (optional, Feld als String)
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $target = null; // z.B. Bus-Nummer

    // Aktiviert/Gesperrt
    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    // Ausgabe-Datum
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $issuedAt = null;

    // Letztes Scandatum
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastScanAt = null;

    // Zusatzinfos (JSON-Feld)
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $extra = null;

    // Kommentar
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    // Erstellt / Geändert (Audit)
    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    // ---- GETTER & SETTER (hier weglassen für Übersicht) ----
    public function __construct()
{
    $this->createdAt = new \DateTime();
}


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    public function setPurpose(?string $purpose): static
    {
        $this->purpose = $purpose;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): static
    {
        $this->target = $target;

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

    public function getIssuedAt(): ?\DateTime
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(?\DateTime $issuedAt): static
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    public function getLastScanAt(): ?\DateTime
    {
        return $this->lastScanAt;
    }

    public function setLastScanAt(?\DateTime $lastScanAt): static
    {
        $this->lastScanAt = $lastScanAt;

        return $this;
    }

    public function getExtra(): ?array
    {
        return $this->extra;
    }

    public function setExtra(?array $extra): static
    {
        $this->extra = $extra;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

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