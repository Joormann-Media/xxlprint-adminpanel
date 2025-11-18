<?php

namespace App\Entity;

use App\Repository\ScriptingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Entity(repositoryClass: ScriptingRepository::class)]
class Scripting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $scriptname = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $scriptowner = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: "scripting_maintainers")]
    private Collection $scriptmaintainer;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $scriptDevstart = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $scriptUpdate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $scriptReadme = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $language = null;

    public function __construct()
    {
        $this->scriptmaintainer = new ArrayCollection();
        $this->scriptDevstart = new DateTime();
        $this->scriptUpdate = new DateTime();
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->scriptUpdate = new DateTime();
    }

    // Getter/Setter

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScriptname(): ?string
    {
        return $this->scriptname;
    }

    public function setScriptname(?string $scriptname): static
    {
        $this->scriptname = $scriptname;
        return $this;
    }
    

    public function setScriptowner(?User $scriptowner): static
    {
        $this->scriptowner = $scriptowner;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getScriptmaintainer(): Collection
    {
        return $this->scriptmaintainer;
    }

    public function addScriptmaintainer(User $user): static
    {
        if (!$this->scriptmaintainer->contains($user)) {
            $this->scriptmaintainer->add($user);
        }
        return $this;
    }

    public function removeScriptmaintainer(User $user): static
    {
        $this->scriptmaintainer->removeElement($user);
        return $this;
    }

    public function getScriptDevstart(): ?\DateTimeInterface
    {
        return $this->scriptDevstart;
    }

    public function setScriptDevstart(?\DateTimeInterface $scriptDevstart): static
    {
        $this->scriptDevstart = $scriptDevstart;
        return $this;
    }

    public function getScriptUpdate(): ?\DateTimeInterface
    {
        return $this->scriptUpdate;
    }

    public function setScriptUpdate(?\DateTimeInterface $scriptUpdate): static
    {
        $this->scriptUpdate = $scriptUpdate;
        return $this;
    }

    public function getScriptReadme(): ?string
    {
        return $this->scriptReadme;
    }

    public function setScriptReadme(?string $scriptReadme): static
    {
        $this->scriptReadme = $scriptReadme;
        return $this;
    }
}
