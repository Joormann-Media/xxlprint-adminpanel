<?php
// src/Entity/Contact.php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Contact
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private int $id;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $firstName = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $lastName = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $email = null;

    #[ORM\Column(type:"string", length:20, nullable:true)]
    private ?string $phone = null;

    #[ORM\Column(type:"string", length:255, nullable:true)]
    private ?string $address = null;

    #[ORM\Column(type:"text", nullable:true)]
    private ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: AddressBook::class, inversedBy: "contacts")]
    private AddressBook $addressBook;

    #[ORM\Column(type:"string", length:255, unique:true)]
    private string $uid;

    

    // ... Getter & Setter ...

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getAddressBook(): ?AddressBook
    {
        return $this->addressBook;
    }

    public function setAddressBook(?AddressBook $addressBook): static
    {
        $this->addressBook = $addressBook;

        return $this;
    }

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;
        return $this;
    }

    public function getVCardData(): string
{
    // Einfachste vCard 4.0 Version, du kannst da noch viel pimpen
    $fn = trim(($this->firstName ?? '') . ' ' . ($this->lastName ?? ''));
    $fn = $fn ?: 'No Name';

    $emailLine = $this->email ? "EMAIL;TYPE=internet:{$this->email}" : '';
    $telLine = $this->phone ? "TEL;TYPE=cell:{$this->phone}" : '';
    $adrLine = $this->address ? "ADR;TYPE=home:;;{$this->address};;;;" : '';
    $noteLine = $this->notes ? "NOTE:{$this->notes}" : '';

    $vcard = <<<VCARD
BEGIN:VCARD
VERSION:4.0
FN:{$fn}
N:{$this->lastName};{$this->firstName};;;
{$emailLine}
{$telLine}
{$adrLine}
{$noteLine}
END:VCARD
VCARD;

    // Leerzeilen entfernen (wegen evtl. leeren Feldern)
    $vcard = preg_replace('/^\s*$/m', '', $vcard);

    return $vcard;
}

}
