<?php

namespace App\Entity;

use App\Repository\TranslationValueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationValueRepository::class)]
class TranslationValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

     #[ORM\ManyToOne(targetEntity: TranslationKey::class, inversedBy: "values")]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?TranslationKey $translationKey = null;

    #[ORM\ManyToOne(targetEntity: Language::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Language $language = null;

    #[ORM\Column(type: 'text')]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getTranslationKey(): ?TranslationKey
    {
        return $this->translationKey;
    }

    public function setTranslationKey(?TranslationKey $translationKey): static
    {
        $this->translationKey = $translationKey;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): static
    {
        $this->language = $language;

        return $this;
    }
}
