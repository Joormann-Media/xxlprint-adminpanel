<?php

namespace App\Entity;

use App\Repository\EMailSignatureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EMailSignatureRepository::class)]
class EMailSignature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $position = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebook = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $twitter = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoPath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bannerPath = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $disclaimer = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $htmlOutput = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]

    #[ORM\Column(type: 'string', length: 50)]
    private string $template = 'classic';

    private ?User $user = null;

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getPosition(): ?string { return $this->position; }
    public function setPosition(?string $position): self { $this->position = $position; return $this; }

    public function getCompany(): ?string { return $this->company; }
    public function setCompany(?string $company): self { $this->company = $company; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }

    public function getMobile(): ?string { return $this->mobile; }
    public function setMobile(?string $mobile): self { $this->mobile = $mobile; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getWebsite(): ?string { return $this->website; }
    public function setWebsite(?string $website): self { $this->website = $website; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }

    public function getLinkedin(): ?string { return $this->linkedin; }
    public function setLinkedin(?string $linkedin): self { $this->linkedin = $linkedin; return $this; }

    public function getFacebook(): ?string { return $this->facebook; }
    public function setFacebook(?string $facebook): self { $this->facebook = $facebook; return $this; }

    public function getTwitter(): ?string { return $this->twitter; }
    public function setTwitter(?string $twitter): self { $this->twitter = $twitter; return $this; }

    public function getLogoPath(): ?string { return $this->logoPath; }
    public function setLogoPath(?string $logoPath): self { $this->logoPath = $logoPath; return $this; }

    public function getBannerPath(): ?string { return $this->bannerPath; }
    public function setBannerPath(?string $bannerPath): self { $this->bannerPath = $bannerPath; return $this; }

    public function getDisclaimer(): ?string { return $this->disclaimer; }
    public function setDisclaimer(?string $disclaimer): self { $this->disclaimer = $disclaimer; return $this; }

    public function getHtmlOutput(): ?string { return $this->htmlOutput; }
    public function setHtmlOutput(?string $htmlOutput): self
{
    $htmlOutput = trim($htmlOutput);

    if (str_starts_with($htmlOutput, '<p>') && str_ends_with($htmlOutput, '</p>')) {
        $htmlOutput = substr($htmlOutput, 3, -4);
    }

    $this->htmlOutput = $htmlOutput;

    return $this;
}

    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }
}
