<?php

namespace App\Entity;

use App\Repository\SystemConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SystemConfigRepository::class)]
class SystemConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // --- Technische Grunddaten
    #[ORM\Column(length: 50)]
    private string $timezone = 'Europe/Berlin';

    #[ORM\Column(length: 10)]
    private string $locale = 'de_DE';

    #[ORM\Column(length: 20)]
    private string $dateFormat = 'd.m.Y H:i:s';

    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';

    // --- Design und Branding
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $systemName = 'Tekath Adminpanel';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $systemLogoUrl = null;

    // --- Sicherheit & Login
    #[ORM\Column(type: 'boolean')]
    private bool $enable2FA = false;

    #[ORM\Column(type: 'integer')]
    private int $sessionTimeout = 30; // Minuten

    // --- Mail & Benachrichtigung
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $supportEmail = null;

    // --- Wartungsmodus
    #[ORM\Column(type: 'boolean')]
    private bool $maintenanceMode = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $maintenanceMessage = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?SystemOwner $systemOwner = null;

    // ... hier kannst du beliebig erweitern!

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): static
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getDateFormat(): ?string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(string $dateFormat): static
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getSystemName(): ?string
    {
        return $this->systemName;
    }

    public function setSystemName(?string $systemName): static
    {
        $this->systemName = $systemName;

        return $this;
    }

    public function getSystemLogoUrl(): ?string
    {
        return $this->systemLogoUrl;
    }

    public function setSystemLogoUrl(?string $systemLogoUrl): static
    {
        $this->systemLogoUrl = $systemLogoUrl;

        return $this;
    }

    public function isEnable2FA(): ?bool
    {
        return $this->enable2FA;
    }

    public function setEnable2FA(bool $enable2FA): static
    {
        $this->enable2FA = $enable2FA;

        return $this;
    }

    public function getSessionTimeout(): ?int
    {
        return $this->sessionTimeout;
    }

    public function setSessionTimeout(int $sessionTimeout): static
    {
        $this->sessionTimeout = $sessionTimeout;

        return $this;
    }

    public function getSupportEmail(): ?string
    {
        return $this->supportEmail;
    }

    public function setSupportEmail(?string $supportEmail): static
    {
        $this->supportEmail = $supportEmail;

        return $this;
    }

    public function isMaintenanceMode(): ?bool
    {
        return $this->maintenanceMode;
    }

    public function setMaintenanceMode(bool $maintenanceMode): static
    {
        $this->maintenanceMode = $maintenanceMode;

        return $this;
    }

    public function getMaintenanceMessage(): ?string
    {
        return $this->maintenanceMessage;
    }

    public function setMaintenanceMessage(?string $maintenanceMessage): static
    {
        $this->maintenanceMessage = $maintenanceMessage;

        return $this;
    }

    public function getSystemOwner(): ?SystemOwner
    {
        return $this->systemOwner;
    }

    public function setSystemOwner(?SystemOwner $systemOwner): static
    {
        $this->systemOwner = $systemOwner;

        return $this;
    }
}