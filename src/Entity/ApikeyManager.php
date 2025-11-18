<?php

namespace App\Entity;

use App\Repository\ApikeyManagerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApikeyManagerRepository::class)]
class ApikeyManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $apiname = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApiname(): ?string
    {
        return $this->apiname;
    }

    public function setApiname(string $apiname): static
    {
        $this->apiname = $apiname;

        return $this;
    }
}
