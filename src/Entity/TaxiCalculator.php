<?php

namespace App\Entity;

use App\Repository\TaxiCalculatorRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: TaxiCalculatorRepository::class)]
#[Vich\Uploadable]  // <---- DAS MUSS HIER HIN!
class TaxiCalculator
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Grundgebühr
    #[ORM\Column(type: 'float')]
    private float $baseFeeDay = 5.90;

    #[ORM\Column(type: 'float')]
    private float $baseFeeNight = 5.90;

    // Meterpreise (pro km, laut Verordnung)
    #[ORM\Column(type: 'float')]
    private float $pricePerKmDay = 2.70;

    #[ORM\Column(type: 'float')]
    private float $pricePerKmNight = 3.00;

    // Meterpreisabschnitt (wie viel Meter pro 0,10€)
    #[ORM\Column(type: 'float')]
    private float $sectionMetersDay = 37.04;

    #[ORM\Column(type: 'float')]
    private float $sectionMetersNight = 33.33;

    // Wartezeitpreise (bis 5min / ab 6min)
    #[ORM\Column(type: 'float')]
    private float $waitPriceFirst5MinHour = 31.80; // €/h

    #[ORM\Column(type: 'float')]
    private float $waitSectionSecondsFirst5Min = 11.32; // sek je 0,10€

    #[ORM\Column(type: 'float')]
    private float $waitPriceFrom6MinHour = 63.40; // €/h

    #[ORM\Column(type: 'float')]
    private float $waitSectionSecondsFrom6Min = 5.68; // sek je 0,10€

    // Zuschläge & Gebühren
    #[ORM\Column(type: 'float')]
    private float $largeCabSurcharge = 7.20;

    #[ORM\Column(type: 'float')]
    private float $withdrawalFee = 11.80; // doppelte Grundgebühr

    // Metadaten
    #[ORM\Column(type: 'datetime')]
    private \DateTime $validFrom;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $regulationPdf = null;

    #[Vich\UploadableField(mapping: 'taxi_calculator_regulations', fileNameProperty: 'regulationPdf')]
    private ?File $regulationPdfFile = null;

    // ... plus Timestamp für Änderungen (optional)
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->validFrom = new \DateTime(); // Standard: Jetzt gültig
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBaseFeeDay(): ?float
    {
        return $this->baseFeeDay;
    }

    public function setBaseFeeDay(float $baseFeeDay): static
    {
        $this->baseFeeDay = $baseFeeDay;

        return $this;
    }

    public function getBaseFeeNight(): ?float
    {
        return $this->baseFeeNight;
    }

    public function setBaseFeeNight(float $baseFeeNight): static
    {
        $this->baseFeeNight = $baseFeeNight;

        return $this;
    }

    public function getPricePerKmDay(): ?float
    {
        return $this->pricePerKmDay;
    }

    public function setPricePerKmDay(float $pricePerKmDay): static
    {
        $this->pricePerKmDay = $pricePerKmDay;

        return $this;
    }

    public function getPricePerKmNight(): ?float
    {
        return $this->pricePerKmNight;
    }

    public function setPricePerKmNight(float $pricePerKmNight): static
    {
        $this->pricePerKmNight = $pricePerKmNight;

        return $this;
    }

    public function getSectionMetersDay(): ?float
    {
        return $this->sectionMetersDay;
    }

    public function setSectionMetersDay(float $sectionMetersDay): static
    {
        $this->sectionMetersDay = $sectionMetersDay;

        return $this;
    }

    public function getSectionMetersNight(): ?float
    {
        return $this->sectionMetersNight;
    }

    public function setSectionMetersNight(float $sectionMetersNight): static
    {
        $this->sectionMetersNight = $sectionMetersNight;

        return $this;
    }

    public function getWaitPriceFirst5MinHour(): ?float
    {
        return $this->waitPriceFirst5MinHour;
    }

    public function setWaitPriceFirst5MinHour(float $waitPriceFirst5MinHour): static
    {
        $this->waitPriceFirst5MinHour = $waitPriceFirst5MinHour;

        return $this;
    }

    public function getWaitSectionSecondsFirst5Min(): ?float
    {
        return $this->waitSectionSecondsFirst5Min;
    }

    public function setWaitSectionSecondsFirst5Min(float $waitSectionSecondsFirst5Min): static
    {
        $this->waitSectionSecondsFirst5Min = $waitSectionSecondsFirst5Min;

        return $this;
    }

    public function getWaitPriceFrom6MinHour(): ?float
    {
        return $this->waitPriceFrom6MinHour;
    }

    public function setWaitPriceFrom6MinHour(float $waitPriceFrom6MinHour): static
    {
        $this->waitPriceFrom6MinHour = $waitPriceFrom6MinHour;

        return $this;
    }

    public function getWaitSectionSecondsFrom6Min(): ?float
    {
        return $this->waitSectionSecondsFrom6Min;
    }

    public function setWaitSectionSecondsFrom6Min(float $waitSectionSecondsFrom6Min): static
    {
        $this->waitSectionSecondsFrom6Min = $waitSectionSecondsFrom6Min;

        return $this;
    }

    public function getLargeCabSurcharge(): ?float
    {
        return $this->largeCabSurcharge;
    }

    public function setLargeCabSurcharge(float $largeCabSurcharge): static
    {
        $this->largeCabSurcharge = $largeCabSurcharge;

        return $this;
    }

    public function getWithdrawalFee(): ?float
    {
        return $this->withdrawalFee;
    }

    public function setWithdrawalFee(float $withdrawalFee): static
    {
        $this->withdrawalFee = $withdrawalFee;

        return $this;
    }

    public function getValidFrom(): ?\DateTime
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTime $validFrom): static
    {
        $this->validFrom = $validFrom;

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

    public function getRegulationPdf(): ?string
    {
        return $this->regulationPdf;
    }

    public function setRegulationPdf(?string $regulationPdf): static
    {
        $this->regulationPdf = $regulationPdf;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    public function getRegulationPdfFile(): ?File
{
    return $this->regulationPdfFile;
}

public function setRegulationPdfFile(?File $regulationPdfFile = null): void
{
    $this->regulationPdfFile = $regulationPdfFile;

    if ($regulationPdfFile) {
        // Update Timestamp, damit Vich uploadet
        $this->updatedAt = new \DateTime();
    }
}



}
