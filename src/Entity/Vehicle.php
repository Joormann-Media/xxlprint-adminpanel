<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $type = null; // z.B. Bus, Taxi, etc.

    

    /**
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="/^[A-Z]{1,3} - [A-Z]{1,2} \d{1,4}$/",
     *     message="Das Kennzeichen muss im Format z.B. WES - TT 203 eingegeben werden."
     * )
     */
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $licensePlate = null; // Kennzeichen

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $vehicleNumber = null; // Interne Bezeichnung

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $seatCount = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $wheelchair = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    private ?Employee $driver = null;

    #[ORM\ManyToOne(targetEntity: LicenceClass::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?LicenceClass $minLicenceClass = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $width = null; // Meter

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $height = null; // Meter

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $length = null; // Meter

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $emptyWeight = null; // kg oder Tonnen

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $maxWeight = null; // kg oder Tonnen

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $maxLoad = null; // kg oder Tonnen

    /**
     * Anzahl der Achsen (Pflichtfeld, min. 1)
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $axleCount = null;

    /**
     * Maximale Achslast in kg oder Tonnen (optional)
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $axleLoad = null;

    // --- NEUE FELDER AB HIER ---

    /**
     * QR-Code Bild-Pfad oder -URL, kann z. B. ein PNG sein.
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vehicleQr = null;

    /**
     * Status des Fahrzeugs: z. B. "active", "werkstatt", "stillgelegt" etc.
     */
    #[ORM\Column(length: 20, nullable: true, options: ['default' => 'active'])]
    private ?string $status = null;

    /**
     * Fahrerinfos (z. B. besondere Hinweise, Text, JSON…)
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $vehicleInfos = null;

    /**
     * Fahrgestellnummer (VIN)
     */
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $vin = null;

    /**
     * Baujahr (4-stellig)
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $buildYear = null;

    /**
     * Datum der Erstzulassung
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $firstRegister = null;

    /**
     * Datum der aktuellen Zulassung
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $currentRegisterDate = null;

    /**
     * Zulassungsstatus (z.B. zugelassen, abgemeldet, außer Betrieb, exportiert ...)
     */
    #[ORM\Column(length: 30, nullable: true, options: ['default' => 'zugelassen'])]
    private ?string $registrationStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $toiletStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $speed100 = null;

    /**
     * Verfügt das Fahrzeug über eine Anhängerkupplung?
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $ahk = null;

    /**
     * Maximale Anhängelast (kg)
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $trailerLoad = null;

    /**
     * Verfügt das Fahrzeug über eine Rollstuhlrampe?
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $wheelchairRamp = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $radiotransceiver = null;

    /**
     * Verfügt das Fahrzeug über einen Lifter?
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $lifter = null;

    /**
     * Konzession (ConcessionManager)
     */
    #[ORM\ManyToOne(targetEntity: ConcessionManager::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?ConcessionManager $concession = null;

    /**
     * Bezeichnung des Fahrzeugs (optional)
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $bezeichnung = null;

    /**
     * Dokumente zum Fahrzeug
     * @var Collection<int, VehicleDocument>
     */
    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: VehicleDocument::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $vehicleDocuments;

    /**
     * Inspektions-/Wartungsintervalle zum Fahrzeug
     * @var Collection<int, VehicleInspectionInterval>
     */
    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: VehicleInspectionInterval::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $vehicleInspectionIntervals;

    /**
     * Fahrtenbuch-Einträge (MileageLog) zum Fahrzeug
     * @var Collection<int, MileageLog>
     */
    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: MileageLog::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $mileageLogs;

    #[ORM\Column(nullable: true)]
    private ?int $ortlogId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ortlogTransid = null;

        // Sanitisierte Version vom Kennzeichen
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $licensePlateSanitized = null;

    // Sanitisierte Version der internen Fahrzeugnummer
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $vehicleNumberSanitized = null;

    // Sanitisierte Sitzanzahl (z. B. als String für flexible Suche)
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $seatCountSanitized = null;

    #[ORM\OneToMany(mappedBy: 'vehicle', targetEntity: VehicleTracking::class)]
    private Collection $trackings;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $standingCapacity = null;


    public function __construct()
    {
        $this->vehicleDocuments = new ArrayCollection();
        $this->vehicleInspectionIntervals = new ArrayCollection();
        $this->mileageLogs = new ArrayCollection();
        $this->trackings = new ArrayCollection();
    }

    /**
     * @return Collection<int, MileageLog>
     */
    public function getMileageLogs(): Collection
    {
        return $this->mileageLogs;
    }
    public function addMileageLog(MileageLog $log): static
    {
        if (!$this->mileageLogs->contains($log)) {
            $this->mileageLogs[] = $log;
            $log->setVehicle($this);
        }
        return $this;
    }
    public function removeMileageLog(MileageLog $log): static
    {
        if ($this->mileageLogs->removeElement($log)) {
            if ($log->getVehicle() === $this) {
                $log->setVehicle(null);
            }
        }
        return $this;
    }

    // --- Getter & Setter ---
    // ... Alle bisherigen ...
    // --- NEU: Collection-Getters/Setters ---

    /** @return Collection<int, VehicleDocument> */
    public function getVehicleDocuments(): Collection
    {
        return $this->vehicleDocuments;
    }
    public function addVehicleDocument(VehicleDocument $doc): static
    {
        if (!$this->vehicleDocuments->contains($doc)) {
            $this->vehicleDocuments[] = $doc;
            $doc->setVehicle($this);
        }
        return $this;
    }
    public function removeVehicleDocument(VehicleDocument $doc): static
    {
        if ($this->vehicleDocuments->removeElement($doc)) {
            if ($doc->getVehicle() === $this) {
                $doc->setVehicle(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, VehicleInspectionInterval> */
    public function getVehicleInspectionIntervals(): Collection
    {
        return $this->vehicleInspectionIntervals;
    }
    public function addVehicleInspectionInterval(VehicleInspectionInterval $interval): static
    {
        if (!$this->vehicleInspectionIntervals->contains($interval)) {
            $this->vehicleInspectionIntervals[] = $interval;
            $interval->setVehicle($this);
        }
        return $this;
    }
    public function removeVehicleInspectionInterval(VehicleInspectionInterval $interval): static
    {
        if ($this->vehicleInspectionIntervals->removeElement($interval)) {
            if ($interval->getVehicle() === $this) {
                $interval->setVehicle(null);
            }
        }
        return $this;
    }

    // ... Rest der bisherigen Getter/Setter ...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }


    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(?string $licensePlate): static
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function getVehicleNumber(): ?string
    {
        return $this->vehicleNumber;
    }

    public function setVehicleNumber(?string $vehicleNumber): static
    {
        $this->vehicleNumber = $vehicleNumber;

        return $this;
    }

    public function getSeatCount(): ?int
    {
        return $this->seatCount;
    }

    public function setSeatCount(int $seatCount): static
    {
        $this->seatCount = $seatCount;

        return $this;
    }

    public function isWheelchair(): ?bool
    {
        return $this->wheelchair;
    }

    public function setWheelchair(bool $wheelchair): static
    {
        $this->wheelchair = $wheelchair;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(?float $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function setLength(?float $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getEmptyWeight(): ?float
    {
        return $this->emptyWeight;
    }

    public function setEmptyWeight(?float $emptyWeight): static
    {
        $this->emptyWeight = $emptyWeight;

        return $this;
    }

    public function getMaxWeight(): ?float
    {
        return $this->maxWeight;
    }

    public function setMaxWeight(?float $maxWeight): static
    {
        $this->maxWeight = $maxWeight;

        return $this;
    }

    public function getMaxLoad(): ?float
    {
        return $this->maxLoad;
    }

    public function setMaxLoad(?float $maxLoad): static
    {
        $this->maxLoad = $maxLoad;

        return $this;
    }

    public function getAxleCount(): ?int
    {
        return $this->axleCount;
    }

    public function setAxleCount(int $axleCount): static
    {
        $this->axleCount = $axleCount;

        return $this;
    }

    public function getAxleLoad(): ?float
    {
        return $this->axleLoad;
    }

    public function setAxleLoad(?float $axleLoad): static
    {
        $this->axleLoad = $axleLoad;

        return $this;
    }

    public function getVehicleQr(): ?string
    {
        return $this->vehicleQr;
    }

    public function setVehicleQr(?string $vehicleQr): static
    {
        $this->vehicleQr = $vehicleQr;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getVehicleInfos(): ?string
    {
        return $this->vehicleInfos;
    }

    public function setVehicleInfos(?string $vehicleInfos): static
    {
        $this->vehicleInfos = $vehicleInfos;

        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(?string $vin): static
    {
        $this->vin = $vin;

        return $this;
    }

    public function getBuildYear(): ?int
    {
        return $this->buildYear;
    }

    public function setBuildYear(?int $buildYear): static
    {
        $this->buildYear = $buildYear;

        return $this;
    }

    public function getFirstRegister(): ?\DateTime
    {
        return $this->firstRegister;
    }

    public function setFirstRegister(?\DateTime $firstRegister): static
    {
        $this->firstRegister = $firstRegister;

        return $this;
    }

    public function getCurrentRegisterDate(): ?\DateTime
    {
        return $this->currentRegisterDate;
    }

    public function setCurrentRegisterDate(?\DateTime $currentRegisterDate): static
    {
        $this->currentRegisterDate = $currentRegisterDate;

        return $this;
    }

    public function getRegistrationStatus(): ?string
    {
        return $this->registrationStatus;
    }

    public function setRegistrationStatus(string $registrationStatus): static
    {
        $this->registrationStatus = $registrationStatus;

        return $this;
    }

    public function getToiletStatus(): ?string
    {
        return $this->toiletStatus;
    }

    public function setToiletStatus(?string $toiletStatus): static
    {
        $this->toiletStatus = $toiletStatus;

        return $this;
    }

    public function getSpeed100(): ?string
    {
        return $this->speed100;
    }

    public function setSpeed100(?string $speed100): static
    {
        $this->speed100 = $speed100;

        return $this;
    }

    public function isAhk(): ?bool
    {
        return $this->ahk;
    }

    public function setAhk(bool $ahk): static
    {
        $this->ahk = $ahk;

        return $this;
    }

    public function getTrailerLoad(): ?float
    {
        return $this->trailerLoad;
    }

    public function setTrailerLoad(?float $trailerLoad): static
    {
        $this->trailerLoad = $trailerLoad;

        return $this;
    }

    public function isWheelchairRamp(): ?bool
    {
        return $this->wheelchairRamp;
    }

    public function setWheelchairRamp(bool $wheelchairRamp): static
    {
        $this->wheelchairRamp = $wheelchairRamp;

        return $this;
    }

    public function isLifter(): ?bool
    {
        return $this->lifter;
    }

    public function setLifter(bool $lifter): static
    {
        $this->lifter = $lifter;

        return $this;
    }

    public function getDriver(): ?Employee
    {
        return $this->driver;
    }

    public function setDriver(?Employee $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    public function getMinLicenceClass(): ?LicenceClass
    {
        return $this->minLicenceClass;
    }

    public function setMinLicenceClass(?LicenceClass $minLicenceClass): static
    {
        $this->minLicenceClass = $minLicenceClass;

        return $this;
    }

    public function getConcession(): ?ConcessionManager
    {
        return $this->concession;
    }

    public function setConcession(?ConcessionManager $concession): static
    {
        $this->concession = $concession;

        return $this;
    }

    public function getBezeichnung(): ?string

    {
        return $this->bezeichnung;
    }

        public function setBezeichnung(?string $bezeichnung): static
        {
            $this->bezeichnung = $bezeichnung;
            return $this;
        }

        public function isRadiotransceiver(): ?bool
        {
            return $this->radiotransceiver;
        }

        public function setRadiotransceiver(?bool $radiotransceiver): static
        {
            $this->radiotransceiver = $radiotransceiver;

            return $this;
        }

        public function getOrtlogId(): ?int
        {
            return $this->ortlogId;
        }

        public function setOrtlogId(?int $ortlogId): static
        {
            $this->ortlogId = $ortlogId;

            return $this;
        }

        public function getOrtlogTransid(): ?string
        {
            return $this->ortlogTransid;
        }

        public function setOrtlogTransid(?string $ortlogTransid): static
        {
            $this->ortlogTransid = $ortlogTransid;

            return $this;
        }

            public function getLicensePlateSanitized(): ?string
    {
        return $this->licensePlateSanitized;
    }

    public function setLicensePlateSanitized(?string $licensePlateSanitized): static
    {
        $this->licensePlateSanitized = $licensePlateSanitized;
        return $this;
    }

    public function getVehicleNumberSanitized(): ?string
    {
        return $this->vehicleNumberSanitized;
    }

    public function setVehicleNumberSanitized(?string $vehicleNumberSanitized): static
    {
        $this->vehicleNumberSanitized = $vehicleNumberSanitized;
        return $this;
    }

    public function getSeatCountSanitized(): ?string
    {
        return $this->seatCountSanitized;
    }

    public function setSeatCountSanitized(?string $seatCountSanitized): static
    {
        $this->seatCountSanitized = $seatCountSanitized;
        return $this;
    }
public function updateSanitizedFields(\App\Service\SanitizerService $sanitizer): void
{
    $this->setLicensePlateSanitized(
        $sanitizer->sanitize($this->getLicensePlate())
    );

    $this->setVehicleNumberSanitized(
        $sanitizer->sanitize($this->getVehicleNumber())
    );

    $this->setSeatCountSanitized(
        $sanitizer->sanitize((string) $this->getSeatCount())
    );
}

    public function getTrackings(): Collection
    {
        return $this->trackings;
    }

    public function addTracking(VehicleTracking $tracking): self
    {
        if (!$this->trackings->contains($tracking)) {
            $this->trackings->add($tracking);
            $tracking->setVehicle($this);
        }

        return $this;
    }

    public function removeTracking(VehicleTracking $tracking): self
    {
        if ($this->trackings->removeElement($tracking)) {
            // set the owning side to null (unless already changed)
            if ($tracking->getVehicle() === $this) {
                $tracking->setVehicle(null);
            }
        }

        return $this;
    }
    public function getStandingCapacity(): ?int
{
    return $this->standingCapacity;
}

public function setStandingCapacity(?int $standingCapacity): static
{
    $this->standingCapacity = $standingCapacity;
    return $this;
}

}