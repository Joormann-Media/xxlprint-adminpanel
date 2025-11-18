<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'project_supervisors')]
    #[Assert\Count(min: 1, minMessage: 'Mindestens ein Betreuer ist erforderlich.')]
    private Collection $supervisors;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $projectDate = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $shortDescription = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $longDescription = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    private ?string $projectStatus = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $projectVersion = null;

    public function __construct()
    {
        $this->supervisors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSupervisors(): Collection
    {
        return $this->supervisors;
    }

    public function addSupervisor(User $user): self
    {
        if (!$this->supervisors->contains($user)) {
            $this->supervisors[] = $user;
        }
        return $this;
    }

    public function removeSupervisor(User $user): self
    {
        $this->supervisors->removeElement($user);
        return $this;
    }

    public function getProjectDate(): ?\DateTimeInterface
    {
        return $this->projectDate;
    }

    public function setProjectDate(\DateTimeInterface $projectDate): self
    {
        $this->projectDate = $projectDate;
        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): self
    {
        $this->longDescription = $longDescription;
        return $this;
    }

    public function getProjectStatus(): ?string
    {
        return $this->projectStatus;
    }

    public function setProjectStatus(string $projectStatus): self
    {
        $this->projectStatus = $projectStatus;
        return $this;
    }

    public function getProjectVersion(): ?string
    {
        return $this->projectVersion;
    }

    public function setProjectVersion(?string $projectVersion): self
    {
        $this->projectVersion = $projectVersion;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Project';
    }
}
