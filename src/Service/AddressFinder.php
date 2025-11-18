<?php

namespace App\Service;

use App\Entity\OfficialAddress;
use App\Repository\OfficialAddressRepository;

class AddressFinder
{
    private OfficialAddressRepository $repo;

    public function __construct(OfficialAddressRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Findet eine OfficialAddress anhand von PLZ, Stadt, Straße und Hausnummer.
     * Gibt null zurück, wenn nichts gefunden.
     */
    public function findByFields(array $fields): ?OfficialAddress
    {
        // Minimalfelder prüfen
        if (empty($fields['zip']) || empty($fields['city']) || empty($fields['street'])) {
            return null;
        }

        $criteria = [
            'postcode'    => $fields['zip'],
            'city'        => $fields['city'],
            'street'      => $fields['street'],
        ];
        if (!empty($fields['streetNo'])) {
            $criteria['houseNumber'] = $fields['streetNo'];
        }

        return $this->repo->findOneBy($criteria);
    }
}
