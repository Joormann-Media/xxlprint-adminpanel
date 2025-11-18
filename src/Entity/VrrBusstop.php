<?php

namespace App\Entity;

use App\Repository\VrrBusstopRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VrrBusstopRepository::class)]
class VrrBusstop
{
    #[ORM\Id]
    #[ORM\Column(name: "STOP_NR", type: 'integer')]
    private ?int $stopNr = null;

    #[ORM\Column(name: "VERSION", type: 'integer')]
    private int $version = 1;

    #[ORM\Column(name: "STOP_TYPE", type: 'integer', nullable: true)]
    private ?int $stopType = null;

    #[ORM\Column(name: "STOP_NAME", type: 'string', length: 255)]
    private string $stopName = '';

    #[ORM\Column(name: "STOP_NAME_WO_LOCALITY", type: 'string', length: 255, nullable: true)]
    private ?string $stopNameWoLocality = null;

    #[ORM\Column(name: "STOP_SHORT_NAME", type: 'string', length: 255, nullable: true)]
    private ?string $stopShortName = null;

    #[ORM\Column(name: "STOP_POS_X", type: 'float', precision: 10, scale: 6, nullable: true)]
    private ?float $stopPosX = null;

    #[ORM\Column(name: "STOP_POS_Y", type: 'float', precision: 10, scale: 6, nullable: true)]
    private ?float $stopPosY = null;

    #[ORM\Column(name: "PLACE", type: 'string', length: 255, nullable: true)]
    private ?string $place = null;

    #[ORM\Column(name: "OCC", type: 'integer', nullable: true)]
    private ?int $occ = null;

    #[ORM\Column(name: "FARE_ZONE_1_NR", type: 'integer', nullable: true)]
    private ?int $fareZone1Nr = null;

    #[ORM\Column(name: "FARE_ZONE_2_NR", type: 'integer', nullable: true)]
    private ?int $fareZone2Nr = null;

    #[ORM\Column(name: "FARE_ZONE_3_NR", type: 'integer', nullable: true)]
    private ?int $fareZone3Nr = null;

    #[ORM\Column(name: "FARE_ZONE_4_NR", type: 'integer', nullable: true)]
    private ?int $fareZone4Nr = null;

    #[ORM\Column(name: "FARE_ZONE_5_NR", type: 'integer', nullable: true)]
    private ?int $fareZone5Nr = null;

    #[ORM\Column(name: "FARE_ZONE_6_NR", type: 'integer', nullable: true)]
    private ?int $fareZone6Nr = null;

    #[ORM\Column(name: "GLOBAL_ID", type: 'string', length: 255)]
    private string $globalId = '';

    #[ORM\Column(name: "VALID_FROM", type: 'date', nullable: true)]
    private ?\DateTimeInterface $validFrom = null;

    #[ORM\Column(name: "VALID_TO", type: 'date', nullable: true)]
    private ?\DateTimeInterface $validTo = null;

    #[ORM\Column(name: "PLACE_ID", type: 'string', length: 36, nullable: true)]
    private ?string $placeId = null;

    #[ORM\Column(name: "GIS_MOT_FLAG", type: 'integer', nullable: true)]
    private ?int $gisMotFlag = null;

    #[ORM\Column(name: "IS_CENTRAL_STOP", type: 'boolean', options: ['default' => false])]
    private bool $isCentralStop = false;

    #[ORM\Column(name: "IS_RESPONSIBLE_STOP", type: 'boolean', options: ['default' => false])]
    private bool $isResponsibleStop = false;

    #[ORM\Column(name: "INTERCHANGE_TYPE", type: 'integer', nullable: true)]
    private ?int $interchangeType = null;

    #[ORM\Column(name: "INTERCHANGE_QUALITY", type: 'integer', nullable: true)]
    private ?int $interchangeQuality = null;

    // ------------ Getters & Setters ------------

    public function getStopNr(): ?int
    {
        return $this->stopNr;
    }

    public function setStopNr(?int $stopNr): self
    {
        $this->stopNr = $stopNr;
        return $this;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function getStopType(): ?int
    {
        return $this->stopType;
    }

    public function setStopType(?int $stopType): self
    {
        $this->stopType = $stopType;
        return $this;
    }

    public function getStopName(): string
    {
        return $this->stopName;
    }

    public function setStopName(string $stopName): self
    {
        $this->stopName = $stopName;
        return $this;
    }

    public function getStopNameWoLocality(): ?string
    {
        return $this->stopNameWoLocality;
    }

    public function setStopNameWoLocality(?string $stopNameWoLocality): self
    {
        $this->stopNameWoLocality = $stopNameWoLocality;
        return $this;
    }

    public function getStopShortName(): ?string
    {
        return $this->stopShortName;
    }

    public function setStopShortName(?string $stopShortName): self
    {
        $this->stopShortName = $stopShortName;
        return $this;
    }

    public function getStopPosX(): ?float
    {
        return $this->stopPosX;
    }

    public function setStopPosX(?float $stopPosX): self
    {
        $this->stopPosX = $stopPosX;
        return $this;
    }

    public function getStopPosY(): ?float
    {
        return $this->stopPosY;
    }

    public function setStopPosY(?float $stopPosY): self
    {
        $this->stopPosY = $stopPosY;
        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): self
    {
        $this->place = $place;
        return $this;
    }

    public function getOcc(): ?int
    {
        return $this->occ;
    }

    public function setOcc(?int $occ): self
    {
        $this->occ = $occ;
        return $this;
    }

    public function getFareZone1Nr(): ?int
    {
        return $this->fareZone1Nr;
    }

    public function setFareZone1Nr(?int $fareZone1Nr): self
    {
        $this->fareZone1Nr = $fareZone1Nr;
        return $this;
    }

    public function getFareZone2Nr(): ?int
    {
        return $this->fareZone2Nr;
    }

    public function setFareZone2Nr(?int $fareZone2Nr): self
    {
        $this->fareZone2Nr = $fareZone2Nr;
        return $this;
    }

    public function getFareZone3Nr(): ?int
    {
        return $this->fareZone3Nr;
    }

    public function setFareZone3Nr(?int $fareZone3Nr): self
    {
        $this->fareZone3Nr = $fareZone3Nr;
        return $this;
    }

    public function getFareZone4Nr(): ?int
    {
        return $this->fareZone4Nr;
    }

    public function setFareZone4Nr(?int $fareZone4Nr): self
    {
        $this->fareZone4Nr = $fareZone4Nr;
        return $this;
    }

    public function getFareZone5Nr(): ?int
    {
        return $this->fareZone5Nr;
    }

    public function setFareZone5Nr(?int $fareZone5Nr): self
    {
        $this->fareZone5Nr = $fareZone5Nr;
        return $this;
    }

    public function getFareZone6Nr(): ?int
    {
        return $this->fareZone6Nr;
    }

    public function setFareZone6Nr(?int $fareZone6Nr): self
    {
        $this->fareZone6Nr = $fareZone6Nr;
        return $this;
    }

    public function getGlobalId(): string
    {
        return $this->globalId;
    }

    public function setGlobalId(string $globalId): self
    {
        $this->globalId = $globalId;
        return $this;
    }

    public function getValidFrom(): ?\DateTimeInterface
    {
        return $this->validFrom;
    }

    public function setValidFrom(?\DateTimeInterface $validFrom): self
    {
        $this->validFrom = $validFrom;
        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(?\DateTimeInterface $validTo): self
    {
        $this->validTo = $validTo;
        return $this;
    }

    public function getPlaceId(): ?string
    {
        return $this->placeId;
    }

    public function setPlaceId(?string $placeId): self
    {
        $this->placeId = $placeId;
        return $this;
    }

    public function getGisMotFlag(): ?int
    {
        return $this->gisMotFlag;
    }

    public function setGisMotFlag(?int $gisMotFlag): self
    {
        $this->gisMotFlag = $gisMotFlag;
        return $this;
    }

    public function isCentralStop(): bool
    {
        return $this->isCentralStop;
    }

    public function setIsCentralStop(bool $isCentralStop): self
    {
        $this->isCentralStop = $isCentralStop;
        return $this;
    }

    public function isResponsibleStop(): bool
    {
        return $this->isResponsibleStop;
    }

    public function setIsResponsibleStop(bool $isResponsibleStop): self
    {
        $this->isResponsibleStop = $isResponsibleStop;
        return $this;
    }

    public function getInterchangeType(): ?int
    {
        return $this->interchangeType;
    }

    public function setInterchangeType(?int $interchangeType): self
    {
        $this->interchangeType = $interchangeType;
        return $this;
    }

    public function getInterchangeQuality(): ?int
    {
        return $this->interchangeQuality;
    }

    public function setInterchangeQuality(?int $interchangeQuality): self
    {
        $this->interchangeQuality = $interchangeQuality;
        return $this;
    }
}
