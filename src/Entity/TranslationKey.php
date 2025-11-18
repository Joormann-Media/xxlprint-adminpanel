<?php

namespace App\Entity;

use App\Repository\TranslationKeyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TranslationKeyRepository::class)]
class TranslationKey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

     #[ORM\Column(length: 255, unique: true)]
    private ?string $key = null; // z.B. "app.greeting"

    #[ORM\OneToMany(mappedBy: "translationKey", targetEntity: TranslationValue::class, cascade: ['persist', 'remove'])]
    private Collection $values;

    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return Collection<int, TranslationValue>
     */
    public function getValues(): Collection
    {
        return $this->values;
    }

    public function addValue(TranslationValue $value): static
    {
        if (!$this->values->contains($value)) {
            $this->values->add($value);
            $value->setTranslationKey($this);
        }

        return $this;
    }

    public function removeValue(TranslationValue $value): static
    {
        if ($this->values->removeElement($value)) {
            // set the owning side to null (unless already changed)
            if ($value->getTranslationKey() === $this) {
                $value->setTranslationKey(null);
            }
        }

        return $this;
    }
}
