<?php
// src/DAV/AddressBookNode.php

namespace App\DAV;

use App\Entity\AddressBook;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sabre\DAV\Collection;
use Sabre\DAV\Exception\NotFound;

class AddressBookNode extends Collection
{
    private EntityManagerInterface $em;
    private AddressBook $addressBook;
    private ContactRepository $contactRepo;

    public function __construct(EntityManagerInterface $em, AddressBook $addressBook, ContactRepository $contactRepo)
    {
        $this->em = $em;
        $this->addressBook = $addressBook;
        $this->contactRepo = $contactRepo;
    }

    public function getName(): string
    {
        return $this->addressBook->getName() ?? 'addressbook-' . $this->addressBook->getId();
    }

    /**
     * @return ContactNode[]
     */
    public function getChildren(): array
    {
        $contacts = $this->contactRepo->findBy(['addressBook' => $this->addressBook]);

        $nodes = [];
        foreach ($contacts as $contact) {
            $nodes[] = new ContactNode($this->em, $contact);
        }
        return $nodes;
    }

    public function getChild($name)
    {
        if (!str_ends_with($name, '.vcf')) {
            throw new NotFound("Contact mit Name $name nicht gefunden.");
        }
        $uid = substr($name, 0, -4);

        $contact = $this->contactRepo->findOneBy(['uid' => $uid, 'addressBook' => $this->addressBook]);

        if (!$contact) {
            throw new NotFound("Contact mit UID $uid nicht gefunden.");
        }

        return new ContactNode($this->em, $contact);
    }
}
