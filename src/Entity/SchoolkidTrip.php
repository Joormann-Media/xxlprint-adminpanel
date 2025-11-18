<?php

namespace App\Entity;

use App\Repository\SchoolkidTripRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolkidTripRepository::class)]
class SchoolkidTrip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Schoolkids::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Schoolkids $schoolkid = null;

    #[ORM\Column(type: 'integer')]
    private int $weekday = 1; // 0=Sonntag

    #[ORM\Column(length: 10)]
    private ?string $tripType = 'hin'; // hin, rueck, extra

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $pickupTime = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $weekPattern = 0; // 0=immer, 1=gerade, 2=ungerade

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pickupAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $specialTrip = false;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $validFrom = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $validTo = null;

    // ---- Abmeldung ----

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $canceled = false; // Fahrt abgemeldet?

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $canceledAt = null; // Wann wurde abgemeldet

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $cancellationType = null; // Email, Tel, SMS, Messenger, Persönlich

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cancellationReason = null; // Freitext (warum)

    // Wer hat die Abmeldung angenommen? (User)
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $canceledReceivedBy = null;
    /**
     * Wer hat die Abmeldung veranlasst? (z.B. Name Elternteil, Schule, Arzt, ...)
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $canceledBy = null;

    // ---- Standard-Getter/Setter hier ... ----

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeekday(): ?int
    {
        return $this->weekday;
    }

    public function setWeekday(int $weekday): static
    {
        $this->weekday = $weekday;

        return $this;
    }

    public function getTripType(): ?string
    {
        return $this->tripType;
    }

    public function setTripType(string $tripType): static
    {
        $this->tripType = $tripType;

        return $this;
    }

    public function getPickupTime(): ?\DateTime
    {
        return $this->pickupTime;
    }

    public function setPickupTime(?\DateTime $pickupTime): static
    {
        $this->pickupTime = $pickupTime;

        return $this;
    }

    public function getWeekPattern(): ?int
    {
        return $this->weekPattern;
    }

    public function setWeekPattern(int $weekPattern): static
    {
        $this->weekPattern = $weekPattern;

        return $this;
    }

    public function getPickupAddress(): ?string
    {
        return $this->pickupAddress;
    }

    public function setPickupAddress(?string $pickupAddress): static
    {
        $this->pickupAddress = $pickupAddress;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = $destination;

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

    public function isSpecialTrip(): ?bool
    {
        return $this->specialTrip;
    }

    public function setSpecialTrip(bool $specialTrip): static
    {
        $this->specialTrip = $specialTrip;

        return $this;
    }

    public function getValidFrom(): ?\DateTime
    {
        return $this->validFrom;
    }

    public function setValidFrom(?\DateTime $validFrom): static
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function getValidTo(): ?\DateTime
    {
        return $this->validTo;
    }

    public function setValidTo(?\DateTime $validTo): static
    {
        $this->validTo = $validTo;

        return $this;
    }

    public function isCanceled(): ?bool
    {
        return $this->canceled;
    }

    public function setCanceled(bool $canceled): static
    {
        $this->canceled = $canceled;

        return $this;
    }

    public function getCanceledAt(): ?\DateTime
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(?\DateTime $canceledAt): static
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    public function getCancellationType(): ?string
    {
        return $this->cancellationType;
    }

    public function setCancellationType(?string $cancellationType): static
    {
        $this->cancellationType = $cancellationType;

        return $this;
    }

    public function getCancellationReason(): ?string
    {
        return $this->cancellationReason;
    }

    public function setCancellationReason(?string $cancellationReason): static
    {
        $this->cancellationReason = $cancellationReason;

        return $this;
    }

    public function getCanceledBy(): ?string
    {
        return $this->canceledBy;
    }

    public function setCanceledBy(?string $canceledBy): static
    {
        $this->canceledBy = $canceledBy;

        return $this;
    }

    public function getSchoolkid(): ?Schoolkids
    {
        return $this->schoolkid;
    }

    public function setSchoolkid(?Schoolkids $schoolkid): static
    {
        $this->schoolkid = $schoolkid;

        return $this;
    }

    public function getCanceledReceivedBy(): ?User
    {
        return $this->canceledReceivedBy;
    }

    public function setCanceledReceivedBy(?User $canceledReceivedBy): static
    {
        $this->canceledReceivedBy = $canceledReceivedBy;

        return $this;
    }
}
