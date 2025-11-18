<?php
namespace App\DAV;

use Sabre\DAV\Collection;
use App\Repository\AddressBookRepository;

class AddressBookHome extends Collection
{
    private $user;
    private $addressBookRepo;

    public function __construct($user, AddressBookRepository $addressBookRepo)
    {
        $this->user = $user;
        $this->addressBookRepo = $addressBookRepo;
    }

    public function getName()
    {
        return 'addressbooks';
    }

    public function getChildren()
    {
        $addressBooks = $this->addressBookRepo->findBy(['user' => $this->user]);

        $nodes = [];
        foreach ($addressBooks as $ab) {
            $nodes[] = new AddressBookNode($ab);
        }
        return $nodes;
    }

    public function childExists($name)
    {
        return (bool) $this->addressBookRepo->findOneBy(['name' => $name, 'user' => $this->user]);
    }

    public function getChild($name)
    {
        $addressBook = $this->addressBookRepo->findOneBy(['name' => $name, 'user' => $this->user]);
        if (!$addressBook) {
            throw new \Sabre\DAV\Exception\NotFound('AddressBook not found');
        }
        return new AddressBookNode($addressBook);
    }
}

