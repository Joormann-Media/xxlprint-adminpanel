<?php

namespace App\Entity;

use App\Repository\WebsiteSettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WebsiteSettingsRepository::class)]
class WebsiteSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $websiteMode = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastUpdate = null;

    #[ORM\Column(length: 255)]
    private ?string $lastUpdateBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $activeUntil = null;

    // ManyToOne-Beziehung zum PopUpManager
    #[ORM\ManyToOne(targetEntity: PopUpManager::class)]
    #[ORM\JoinColumn(name: 'WebsiteMessageId', referencedColumnName: 'id', nullable: true, onDelete: "SET NULL")]
    private ?PopUpManager $WebsiteMessageId = null;  // Verweist auf PopUpManager-Objekt
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWebsiteMode(): ?string
    {
        return $this->websiteMode;
    }

    public function setWebsiteMode(string $websiteMode): static
    {
        $this->websiteMode = $websiteMode;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTimeInterface $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getLastUpdateBy(): ?string
    {
        return $this->lastUpdateBy;
    }

    public function setLastUpdateBy(string $lastUpdateBy): static
    {
        $this->lastUpdateBy = $lastUpdateBy;

        return $this;
    }

    public function getActiveUntil(): ?\DateTimeInterface
    {
        return $this->activeUntil;
    }

    public function setActiveUntil(?\DateTimeInterface $activeUntil): static
    {
        $this->activeUntil = $activeUntil;

        return $this;
    }

    // Getter und Setter für das PopUpManager-Objekt
    public function getWebsiteMessageId(): ?PopUpManager
    {
        return $this->WebsiteMessageId;
    }
    
    public function setWebsiteMessageId(?PopUpManager $WebsiteMessageId): static
    {
        $this->WebsiteMessageId = $WebsiteMessageId;
    
        return $this;
    }
}
