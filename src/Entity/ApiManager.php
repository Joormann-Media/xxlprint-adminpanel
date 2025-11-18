<?php

namespace App\Entity;

use App\Repository\ApiManagerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiManagerRepository::class)]
class ApiManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $maintainer = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $apiCreate = null;

    #[ORM\Column(length: 50)]
    private ?string $apiStatus = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $apiDescription = null;

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

    // Getter und Setter
    public function getMaintainer(): ?User { return $this->maintainer; }
    public function setMaintainer(User $maintainer): static { $this->maintainer = $maintainer; return $this; }

    public function getApiCreate(): ?\DateTimeImmutable { return $this->apiCreate; }
    public function setApiCreate(\DateTimeImmutable $apiCreate): static { $this->apiCreate = $apiCreate; return $this; }

    public function getApiStatus(): ?string { return $this->apiStatus; }
    public function setApiStatus(string $apiStatus): static { $this->apiStatus = $apiStatus; return $this; }

    public function getApiDescription(): ?string { return $this->apiDescription; }
    public function setApiDescription(?string $apiDescription): static { $this->apiDescription = $apiDescription; return $this; }
}
