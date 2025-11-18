<?php

namespace App\Entity;

use App\Repository\OfficialAddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfficialAddressRepository::class)]
#[ORM\Index(columns: ['postcode'])]
#[ORM\Index(columns: ['city'])]
#[ORM\Index(columns: ['street'])]
#[ORM\Index(columns: ['district'])]
class OfficialAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private string $postcode;

    #[ORM\Column(length: 100)]
    private string $city;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $district = null;

    #[ORM\Column(length: 150)]
    private string $street;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $houseNumber = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $houseNumberRange = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $lat = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $lon = null;

    #[ORM\Column(length: 20)]
    private string $country = 'DE';

    #[ORM\Column(length: 30)]
    private string $source = 'manual';

#[ORM\Column(type: 'datetime_immutable')]
private \DateTimeImmutable $createdAt;

#[ORM\Column(type: 'datetime', nullable: true)]
private ?\DateTimeInterface $updatedAt = null;

#[ORM\Column(length: 100, nullable: true)]
private ?string $neighbourhood = null;

#[ORM\Column(length: 100, nullable: true)]
private ?string $subdistrict = null;

#[ORM\Column(length: 255, nullable: true)]
private ?string $locationComment = null;

#[ORM\Column(length: 255, nullable: true)]
private ?string $normalized = null;

#[ORM\Column(type: 'boolean', options: ['default' => false], nullable: false)]
private bool $valid = false;




    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // src/Entity/OfficialAddress.php

public function __toString()
{
    return sprintf(
        '%s %s, %s %s (%s)',
        $this->getStreet(),
        $this->getHouseNumber(),
        $this->getPostcode(),
        $this->getCity(),
        $this->getDistrict()
    );
}


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): static
    {
        $this->postcode = $postcode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): static
    {
        $this->district = $district;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;
        return $this;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(?string $houseNumber): static
    {
        $this->houseNumber = $houseNumber;
        return $this;
    }

    public function getHouseNumberRange(): ?string
    {
        return $this->houseNumberRange;
    }

    public function setHouseNumberRange(?string $houseNumberRange): static
    {
        $this->houseNumberRange = $houseNumberRange;
        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): static
    {
        $this->lat = $lat;
        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(?float $lon): static
    {
        $this->lon = $lon;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
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
    public function getNeighbourhood(): ?string
{
    return $this->neighbourhood;
}

public function setNeighbourhood(?string $neighbourhood): static
{
    $this->neighbourhood = $neighbourhood;
    return $this;
}

public function getSubdistrict(): ?string
{
    return $this->subdistrict;
}

public function setSubdistrict(?string $subdistrict): static
{
    $this->subdistrict = $subdistrict;
    return $this;
}

public function getLocationComment(): ?string
{
    return $this->locationComment;
}

public function setLocationComment(?string $locationComment): static
{
    $this->locationComment = $locationComment;
    return $this;
}

public function getNormalized(): ?string
{
    return $this->normalized;
}

public function setNormalized(?string $normalized): static
{
    $this->normalized = $normalized;
    return $this;
}


public static function buildNormalized(
    ?string $street,
    ?string $houseNumber,
    ?string $postcode,
    ?string $city
): string {
    $full = trim(sprintf('%s %s %s %s', $street, $houseNumber, $postcode, $city));
    $full = mb_strtolower($full);
    $full = strtr($full, [
        'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
    ]);
    // Straße vereinheitlichen:
    $full = preg_replace('/\bstr\.\b/', 'strasse', $full);
    $full = preg_replace('/\bstraße\b/', 'strasse', $full);
    $full = preg_replace('/[^a-z0-9 ]+/u', '', $full); // Sonderzeichen raus
    $full = preg_replace('/\s+/', ' ', $full);
    return trim($full);
}
public function updateNormalized(): void
{
    $this->normalized = self::buildNormalized(
        $this->getStreet(),
        $this->getHouseNumber(),
        $this->getPostcode(),
        $this->getCity()
    );
}

public function isValid(): ?bool
{
    return $this->valid;
}
public function setValid(bool $valid): static
{
    $this->valid = $valid ? true : false;
    return $this;
}



}
