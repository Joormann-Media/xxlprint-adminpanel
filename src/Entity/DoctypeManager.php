<?php

namespace App\Entity;

use App\Repository\DoctypeManagerRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User; // <-- Relation!

#[ORM\Entity(repositoryClass: DoctypeManagerRepository::class)]
class DoctypeManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Name des Dokumententyps (z.B. "Fahrzeugschein")
     */
    #[ORM\Column(length: 100)]
    private ?string $doctypeName = null;

    /**
     * Art/Kategorie des Dokuments (z.B. "PDF", "Bild", "Versicherung", ...)
     */
    #[ORM\Column(length: 50)]
    private ?string $doctypeType = null;

    /**
     * User, der den Dokumententyp angelegt hat
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $doctypeUser = null;

    /**
     * Zeitpunkt der Erstellung (deutscher Zeitstempel)
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $doctypeAdd = null;

    // --- Getter & Setter ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDoctypeName(): ?string
    {
        return $this->doctypeName;
    }

    public function setDoctypeName(string $doctypeName): static
    {
        $this->doctypeName = $doctypeName;
        return $this;
    }

    public function getDoctypeType(): ?string
    {
        return $this->doctypeType;
    }

    public function setDoctypeType(string $doctypeType): static
    {
        $this->doctypeType = $doctypeType;
        return $this;
    }

    public function getDoctypeUser(): ?User
    {
        return $this->doctypeUser;
    }

    public function setDoctypeUser(User $doctypeUser): static
    {
        $this->doctypeUser = $doctypeUser;
        return $this;
    }

    public function getDoctypeAdd(): ?\DateTimeImmutable
    {
        return $this->doctypeAdd;
    }

    public function setDoctypeAdd(\DateTimeImmutable $doctypeAdd): static
    {
        $this->doctypeAdd = $doctypeAdd;
        return $this;
    }
        public function __toString(): string
    {
        return $this->doctypeName ?? ''; // Gib das Feld zurück, das den Namen enthält
    }
}
