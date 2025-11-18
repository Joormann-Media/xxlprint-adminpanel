<?php

namespace App\Entity;

use App\Repository\ReleaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\ReleaseFile;


#[ORM\Entity(repositoryClass: ReleaseRepository::class)]
#[ORM\Table(name: "releases")]


class Release
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $softwareId;

    #[ORM\Column(length: 150)]
    private string $softwareName;

    #[ORM\Column(length: 20)]
    private string $version;

    #[ORM\Column]
    private ?string $releaseDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $downloadUrl = null;

    #[ORM\Column(type: 'text')]
    private string $releaseNotes;

    #[ORM\Column]
    private bool $isPublic = false;

    #[ORM\Column(length: 50)]
    private string $platform;

    #[ORM\Column(length: 100)]
    private string $releaseCreatedBy;

    #[ORM\Column]
    private string $releaseCreatedAt;

    #[ORM\Column(length: 20)]
    private string $releaseDevStatus;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\OneToMany(mappedBy: 'release', targetEntity: ReleaseFile::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $files;

    public function __construct()
{
    $this->softwareId = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
    $this->version = '0.0.1'; // ✅ Hier setzen!
    $this->files = new ArrayCollection();

}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSoftwareId(): ?string
    {
        return $this->softwareId;
    }

    public function setSoftwareId(string $softwareId): static
    {
        $this->softwareId = $softwareId;

        return $this;
    }

    public function getSoftwareName(): ?string
    {
        return $this->softwareName;
    }

    public function setSoftwareName(string $softwareName): static
    {
        $this->softwareName = $softwareName;

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version ?? '0.0.1';
    }

    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getReleaseDate(): ?string
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(string $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getDownloadUrl(): ?string
    {
        return $this->downloadUrl;
    }

    public function setDownloadUrl(string $downloadUrl): static
    {
        $this->downloadUrl = $downloadUrl;

        return $this;
    }

    public function getReleaseNotes(): ?string
    {
        return $this->releaseNotes;
    }

    public function setReleaseNotes(string $releaseNotes): static
    {
        $this->releaseNotes = $releaseNotes;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): static
    {
        $this->platform = $platform;

        return $this;
    }

    public function getReleaseCreatedBy(): ?string
    {
        return $this->releaseCreatedBy;
    }

    public function setReleaseCreatedBy(string $releaseCreatedBy): static
    {
        $this->releaseCreatedBy = $releaseCreatedBy;

        return $this;
    }

    public function getReleaseCreatedAt(): ?string
    {
        return $this->releaseCreatedAt;
    }

    public function setReleaseCreatedAt(string $releaseCreatedAt): static
    {
        $this->releaseCreatedAt = $releaseCreatedAt;

        return $this;
    }

    public function getReleaseDevStatus(): ?string
    {
        return $this->releaseDevStatus;
    }

    public function setReleaseDevStatus(string $releaseDevStatus): static
    {
        $this->releaseDevStatus = $releaseDevStatus;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFiles(): Collection
{
    return $this->files;
}

public function addFile(ReleaseFile $file): static
{
    if (!$this->files->contains($file)) {
        $this->files[] = $file;
        $file->setRelease($this);
    }

    return $this;
}

public function removeFile(ReleaseFile $file): static
{
    if ($this->files->removeElement($file)) {
        // Set the owning side to null (unless already changed)
        if ($file->getRelease() === $this) {
            $file->setRelease(null);
        }
    }

    return $this;
}

}
