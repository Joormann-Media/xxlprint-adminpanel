<?php

namespace App\Entity;

use App\Repository\VehicleDocumentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleDocumentRepository::class)]
class VehicleDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    // Bezug auf Fahrzeug (Vehicle)
    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicle $vehicle = null;

    // Dokumenten-Typ: Relation zu DoctypeManager!
    #[ORM\ManyToOne(targetEntity: DoctypeManager::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?DoctypeManager $vehicleDoctype = null;

    // Dokument-Bild/Dateiname/Pfad
    #[ORM\Column(type: 'string', length: 255)]
    private string $vehicleDocimage;

    // Bezug auf User, der das Dokument hochgeladen hat
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $vehicleDocuser = null;

    // Hochlade-Datum/Zeit
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $vehicleDocadd;

    // -- Getter/Setter --

    public function getId(): ?int { return $this->id; }

    public function getVehicle(): ?Vehicle { return $this->vehicle; }
    public function setVehicle(?Vehicle $vehicle): self { $this->vehicle = $vehicle; return $this; }

    /** @return DoctypeManager|null */
    public function getVehicleDoctype(): ?DoctypeManager { return $this->vehicleDoctype; }
    public function setVehicleDoctype(?DoctypeManager $doctype): self { $this->vehicleDoctype = $doctype; return $this; }

    public function getVehicleDocimage(): string { return $this->vehicleDocimage; }
    public function setVehicleDocimage(string $docimage): self { $this->vehicleDocimage = $docimage; return $this; }

    public function getVehicleDocuser(): ?User { return $this->vehicleDocuser; }
    public function setVehicleDocuser(?User $user): self { $this->vehicleDocuser = $user; return $this; }

    public function getVehicleDocadd(): \DateTimeInterface { return $this->vehicleDocadd; }
    public function setVehicleDocadd(\DateTimeInterface $dt): self { $this->vehicleDocadd = $dt; return $this; }
}
