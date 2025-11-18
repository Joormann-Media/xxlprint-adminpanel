<?php

namespace App\Entity;

use App\Repository\FileChangeLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileChangeLogRepository::class)]
class FileChangeLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Vollständiger Pfad der betroffenen Datei
     */
    #[ORM\Column(type: 'string', length: 1024)]
    private string $filePath;

    /**
     * Typ des Events (CREATE, MODIFY, DELETE, MOVE)
     */
    #[ORM\Column(type: 'string', length: 16)]
    private string $eventType;

    /**
     * Zeitpunkt des Events
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $eventTime;

    /**
     * Optional: Alter Pfad (bei MOVE)
     */
    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private ?string $oldFilePath = null;

    /**
     * Optional: Dateigröße nach Event (in Bytes)
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $fileSize = null;

    /**
     * Optional: Benutzer/Prozess, der das Event ausgelöst hat
     */
    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $user = null;

    // ========== GETTER & SETTER ==========

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): self
    {
        $this->eventType = strtoupper($eventType);
        return $this;
    }

    public function getEventTime(): \DateTimeInterface
    {
        return $this->eventTime;
    }

    public function setEventTime(\DateTimeInterface $eventTime): self
    {
        $this->eventTime = $eventTime;
        return $this;
    }

    public function getOldFilePath(): ?string
    {
        return $this->oldFilePath;
    }

    public function setOldFilePath(?string $oldFilePath): self
    {
        $this->oldFilePath = $oldFilePath;
        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(?int $fileSize): self
    {
        $this->fileSize = $fileSize;
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
}