<?php

namespace App\Entity;

use App\Repository\PopUpManagerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PopUpManagerRepository::class)]
class PopUpManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $popupName = null;

    #[ORM\Column(length: 255)]
    private ?string $popupStatus = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $popupExpires = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $popupCreate = null;

    #[ORM\Column(length: 255)]
    private ?string $popupUser = null;

    #[ORM\Column(length: 255)]
    private ?string $popupDescription = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $popupContent = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $popupActiveFrom = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $popupCategory = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPopupName(): ?string
    {
        return $this->popupName;
    }

    public function setPopupName(string $popupName): static
    {
        $this->popupName = $popupName;

        return $this;
    }

    public function getPopupStatus(): ?string
    {
        return $this->popupStatus;
    }

    public function setPopupStatus(string $popupStatus): static
    {
        $this->popupStatus = $popupStatus;

        return $this;
    }

    public function getPopupExpires(): ?\DateTimeInterface
    {
        return $this->popupExpires;
    }

    public function setPopupExpires(\DateTimeInterface $popupExpires): static
    {
        $this->popupExpires = $popupExpires;

        return $this;
    }

    public function getPopupCreate(): ?\DateTimeInterface
    {
        return $this->popupCreate;
    }

    public function setPopupCreate(\DateTimeInterface $popupCreate): static
    {
        $this->popupCreate = $popupCreate;

        return $this;
    }

    public function getPopupUser(): ?string
    {
        return $this->popupUser;
    }

    public function setPopupUser(string $popupUser): static
    {
        $this->popupUser = $popupUser;

        return $this;
    }

    public function getPopupDescription(): ?string
    {
        return $this->popupDescription;
    }

    public function setPopupDescription(string $popupDescription): static
    {
        $this->popupDescription = $popupDescription;

        return $this;
    }

    public function getPopupContent(): ?string
    {
        return $this->popupContent;
    }

    public function setPopupContent(string $popupContent): static
    {
        $this->popupContent = $popupContent;

        return $this;
    }

    public function getPopupActiveFrom(): ?\DateTimeInterface
    {
        return $this->popupActiveFrom;
    }

    public function setPopupActiveFrom(\DateTimeInterface $popupActiveFrom): static
    {
        $this->popupActiveFrom = $popupActiveFrom;

        return $this;
    }

    public function getPopupCategory(): ?string
    {
        return $this->popupCategory;
    }

    public function setPopupCategory(string $popupCategory): static
    {
        $this->popupCategory = $popupCategory;

        return $this;
    }
}
