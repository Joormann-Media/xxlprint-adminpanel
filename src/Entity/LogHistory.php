<?php

namespace App\Entity;

use App\Repository\LogHistoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogHistoryRepository::class)]
class LogHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $target = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $userID = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $username = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $logdump = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(string $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function getUserID(): ?string
    {
        return $this->userID;
    }

    public function setUserID(string $userID): static
    {
        $this->userID = $userID;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTime $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getLogdump(): ?string
    {
        return $this->logdump;
    }

    public function setLogdump(string $logdump): static
    {
        $this->logdump = $logdump;

        return $this;
    }

    
}
