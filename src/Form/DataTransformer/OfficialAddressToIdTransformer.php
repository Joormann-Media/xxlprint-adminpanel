<?php

// src/Form/DataTransformer/OfficialAddressToIdTransformer.php

namespace App\Form\DataTransformer;

use App\Entity\OfficialAddress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class OfficialAddressToIdTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function transform($address): ?string {
        return $address?->getId();
    }

    public function reverseTransform($id): ?OfficialAddress {
        if (!$id) return null;
        $address = $this->em->getRepository(OfficialAddress::class)->find($id);
        if (!$address) throw new TransformationFailedException('Adresse nicht gefunden!');
        return $address;
    }
}

