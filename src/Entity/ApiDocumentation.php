<?php

namespace App\Entity;

use App\Repository\ApiDocumentationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiDocumentationRepository::class)]
class ApiDocumentation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ApiManager::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ApiManager $apiManager = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $documentation_short = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $documentation = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createBy = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastUpdate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function getApiManager(): ?ApiManager
    {
        return $this->apiManager;
    }

    public function setApiManager(ApiManager $apiManager): static
    {
        $this->apiManager = $apiManager;

        return $this;
    }

    public function getDocumentationShort(): ?string
    {
        return $this->documentation_short;
    }

    public function setDocumentationShort(?string $documentation_short): static
    {
        $this->documentation_short = $documentation_short;

        return $this;
    }

    public function getDocumentation(): ?string
    {
        return $this->documentation;
    }

    public function setDocumentation(?string $documentation): static
    {
        $this->documentation = $documentation;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getCreateBy(): ?User
    {
        return $this->createBy;
    }

    public function setCreateBy(User $createBy): static
    {
        $this->createBy = $createBy;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeImmutable
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?\DateTimeImmutable $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }
}
