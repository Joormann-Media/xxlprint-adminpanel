<?php

namespace App\Entity;

use App\Repository\AuftraggeberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuftraggeberRepository::class)]
class Auftraggeber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ansprechpartner = null;

    #[ORM\Column(length: 255)]
    private ?string $strasse = null;

    #[ORM\Column(length: 20)]
    private ?string $strasseNr = null;

    #[ORM\Column(length: 10)]
    private ?string $plz = null;

    #[ORM\Column(length: 100)]
    private ?string $stadt = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $telefon = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: OfficialAddress::class)]
private ?OfficialAddress $address = null;

    // ---------- Getter & Setter ----------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAnsprechpartner(): ?string
    {
        return $this->ansprechpartner;
    }

    public function setAnsprechpartner(?string $ansprechpartner): static
    {
        $this->ansprechpartner = $ansprechpartner;
        return $this;
    }

    public function getStrasse(): ?string
    {
        return $this->strasse;
    }

    public function setStrasse(string $strasse): static
    {
        $this->strasse = $strasse;
        return $this;
    }

    public function getStrasseNr(): ?string
    {
        return $this->strasseNr;
    }

    public function setStrasseNr(string $strasseNr): static
    {
        $this->strasseNr = $strasseNr;
        return $this;
    }

    public function getPlz(): ?string
    {
        return $this->plz;
    }

    public function setPlz(string $plz): static
    {
        $this->plz = $plz;
        return $this;
    }

    public function getStadt(): ?string
    {
        return $this->stadt;
    }

    public function setStadt(string $stadt): static
    {
        $this->stadt = $stadt;
        return $this;
    }

    public function getTelefon(): ?string
    {
        return $this->telefon;
    }

    public function setTelefon(?string $telefon): static
    {
        $this->telefon = $telefon;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
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
