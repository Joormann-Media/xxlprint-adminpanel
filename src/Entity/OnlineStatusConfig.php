<?php

namespace App\Entity;

use App\Repository\OnlineStatusConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: OnlineStatusConfigRepository::class)]
#[Vich\Uploadable]
class OnlineStatusConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iconName = null;

    #[Vich\UploadableField(mapping: 'online_status_icons', fileNameProperty: 'iconName')]
    #[Assert\File(
        maxSize: '2M',
        mimeTypes: ['image/png', 'image/jpeg', 'image/svg+xml'],
        mimeTypesMessage: 'Erlaubt sind nur PNG, JPG oder SVG'
    )]
    private ?File $iconFile = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getIconName(): ?string
    {
        return $this->iconName;
    }

    public function setIconName(?string $iconName): static
    {
        $this->iconName = $iconName;
        return $this;
    }

    public function setIconFile(?File $iconFile = null): static
    {
        $this->iconFile = $iconFile;
        if ($iconFile !== null) {
            $this->updatedAt = new \DateTime();
        }
        return $this;
    }

    public function getIconFile(): ?File
    {
        return $this->iconFile;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
