<?php

namespace App\Entity;

use App\Repository\ModuleManagerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleManagerRepository::class)]
class ModuleManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $create = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $maintainer;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastUpdate = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $logId = null;

    #[ORM\Column(type: 'json')]
    private array $correspondingFiles = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $mappedEntitys = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $readme = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $moduleID = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $dependencies = null;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: ModuleBreadcrumb::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $breadcrumbs;

    public function __construct()
    {
        $this->maintainer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreate(): ?\DateTimeInterface
    {
        return $this->create;
    }

    public function setCreate(?\DateTimeInterface $create): static
    {
        $this->create = $create;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMaintainer(): Collection
    {
        return $this->maintainer;
    }

    public function addMaintainer(User $user): static
    {
        if (!$this->maintainer->contains($user)) {
            $this->maintainer->add($user);
        }
        return $this;
    }

    public function removeMaintainer(User $user): static
    {
        $this->maintainer->removeElement($user);
        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?\DateTimeInterface $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getLogId(): ?string
    {
        return $this->logId;
    }

    public function setLogId(?string $logId): static
    {
        $this->logId = $logId;

        return $this;
    }

    public function getCorrespondingFiles(): ?array
    {
        return $this->correspondingFiles;
    }

    public function setCorrespondingFiles(?array $correspondingFiles): static
    {
        $this->correspondingFiles = $correspondingFiles;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

public function getMappedEntitys(): ?array
{
    return $this->mappedEntitys;
}
public function setMappedEntitys(?array $mappedEntitys): static
{
    $this->mappedEntitys = $mappedEntitys;
    return $this;
}


    public function getReadme(): ?string
    {
        return $this->readme;
    }

    public function setReadme(?string $readme): static
    {
        $this->readme = $readme;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getModuleID(): ?string
    {
        return $this->moduleID;
    }

    public function setModuleID(?string $moduleID): static
    {
        $this->moduleID = $moduleID;

        return $this;
    }

    public function getDependencies(): ?string
    {
        return $this->dependencies;
    }

    public function setDependencies(?string $dependencies): static
    {
        $this->dependencies = $dependencies;

        return $this;
    }
}
