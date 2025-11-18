<?php

namespace App\Entity;

use App\Repository\SecurityAccessLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecurityAccessLogRepository::class)]
class SecurityAccessLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Zeitstempel des Events (Login/Logout etc.)
     */
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $timestamp = null;

    /**
     * Benutzername (z. B. Linux-User, VSCode-User, SFTP-User etc.)
     */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $user = null;

    /**
     * Quell-IP-Adresse
     */
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $sourceIp = null;

    /**
     * Typ des Zugriffs (SSH, VSCode, SFTP, Web, ...)
     */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $sessionType = null;

    /**
     * Status: success, failed, logout, timeout, etc.
     */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $status = null;

    /**
     * Freitext oder Metadaten (Client-String, Pfad, SSH-Key-Fingerprint, ...)
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $details = null;

    // ========== GETTER & SETTER ==========

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getSourceIp(): ?string
    {
        return $this->sourceIp;
    }

    public function setSourceIp(?string $sourceIp): self
    {
        $this->sourceIp = $sourceIp;
        return $this;
    }

    public function getSessionType(): ?string
    {
        return $this->sessionType;
    }

    public function setSessionType(?string $sessionType): self
    {
        $this->sessionType = $sessionType;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;
        return $this;
    }
}