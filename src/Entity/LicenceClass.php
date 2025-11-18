<?php

namespace App\Entity;

use App\Repository\LicenceClassRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: LicenceClassRepository::class)]
class LicenceClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $shortName = null;   // z.B. B, BE, C1, D1

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $description = null; // z.B. "Kraftfahrzeuge bis 3,5t, max. 8 Sitzplätze"

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $infoBox = null;     // Freitext: Was darf ich mit dieser Klasse fahren?

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'includedBy')]
    #[ORM\JoinTable(
        name: 'licence_class_includes',
        joinColumns: [new ORM\JoinColumn(name: 'parent_class_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'included_class_id', referencedColumnName: 'id')]
    )]
    private Collection $includes;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'includes')]
    private Collection $includedBy;

    public function __construct()
    {
        $this->includes = new ArrayCollection();
        $this->includedBy = new ArrayCollection();
    }

    /**
     * @return Collection<int, LicenceClass>
     */
    public function getIncludes(): Collection
    {
        return $this->includes;
    }

    public function addInclude(self $licenceClass): self
    {
        if (!$this->includes->contains($licenceClass)) {
            $this->includes->add($licenceClass);
        }
        return $this;
    }

    public function removeInclude(self $licenceClass): self
    {
        $this->includes->removeElement($licenceClass);
        return $this;
    }

    /**
     * @return Collection<int, LicenceClass>
     */
    public function getIncludedBy(): Collection
    {
        return $this->includedBy;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): self
    {
        $this->shortName = $shortName;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getInfoBox(): ?string
    {
        return $this->infoBox;
    }

    public function setInfoBox(?string $infoBox): self
    {
        $this->infoBox = $infoBox;
        return $this;
    }

    public function __toString(): string
    {
        // Damit die Klasse in Dropdowns etc. sinnvoll angezeigt wird
        return $this->shortName . ' - ' . $this->description;
    }
}
