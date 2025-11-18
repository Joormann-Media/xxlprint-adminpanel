<?php
// src/DAV/CardDAV/CardDavBackend.php

namespace App\DAV\CardDAV;

use App\Entity\Contact;
use App\Entity\AddressBook;
use Doctrine\ORM\EntityManagerInterface;
use Sabre\CardDAV\Backend\AbstractBackend;
use Sabre\CardDAV\Backend\SyncSupport;
use Sabre\DAV\Exception\NotFound;
use Sabre\DAV\Exception\Forbidden;
use Sabre\DAV\PropPatch;

class CardDavBackend extends AbstractBackend implements SyncSupport
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getAddressBooksForUser($principalUri)
    {
        $userId = basename($principalUri); // z. B. 'principals/bea' → 'bea'
        $repo = $this->em->getRepository(AddressBook::class);
        $books = $repo->findBy(['owner' => $userId]);

        $result = [];
        foreach ($books as $book) {
            $result[] = [
                'id'                => $book->getId(),
                'uri'               => 'addressbook-' . $book->getId(),
                'principaluri'      => $principalUri,
                'displayname'       => $book->getName(),
                '{DAV:}sync-token'  => '1',
            ];
        }
        return $result;
    }

    public function createAddressBook($principalUri, $url, array $properties)
    {
        throw new Forbidden('Read-only address books');
    }

    public function updateAddressBook($addressBookId, PropPatch $propPatch)
    {
        throw new Forbidden('Read-only address books');
    }

    public function deleteAddressBook($addressBookId)
    {
        throw new Forbidden('Read-only address books');
    }

    public function getCards($addressBookId)
    {
        $repo = $this->em->getRepository(Contact::class);
        $contacts = $repo->findBy(['addressBook' => $addressBookId]);

        $cards = [];
        foreach ($contacts as $contact) {
            $cards[] = [
                'carddata' => $contact->getVCardData(),
                'uri' => $contact->getUid() . '.vcf',
                'lastmodified' => time(),
            ];
        }
        return $cards;
    }

    // Wichtig: Keine Typ-Hints bei Parametern oder Rückgabe (interface-konform)
    public function getCard($addressBookId, $cardUri)
    {
        $uid = basename($cardUri, '.vcf');
        $repo = $this->em->getRepository(Contact::class);
        $contact = $repo->findOneBy(['uid' => $uid, 'addressBook' => $addressBookId]);

        if (!$contact) {
            throw new NotFound("Card not found");
        }

        return [
            'carddata' => $contact->getVCardData(),
            'uri' => $contact->getUid() . '.vcf',
            'lastmodified' => time(),
        ];
    }

    public function createCard($addressBookId, $cardUri, $cardData)
    {
        throw new Forbidden('Read-only address books');
    }

    public function updateCard($addressBookId, $cardUri, $cardData)
    {
        throw new Forbidden('Read-only address books');
    }

    public function deleteCard($addressBookId, $cardUri)
    {
        throw new Forbidden('Read-only address books');
    }

    public function getChangesForAddressBook($addressBookId, $syncToken, $syncLevel, $limit = null)
    {
        return [
            'syncToken' => '1',
            'added' => [],
            'modified' => [],
            'deleted' => [],
        ];
    }
}
