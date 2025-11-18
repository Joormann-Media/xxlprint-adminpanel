<?php
// src/DAV/ContactNode.php

namespace App\DAV;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Sabre\DAV\File;
use Sabre\DAV\Exception\NotFound;

class ContactNode extends File
{
    private EntityManagerInterface $em;
    private Contact $contact;

    public function __construct(EntityManagerInterface $em, Contact $contact)
    {
        $this->em = $em;
        $this->contact = $contact;
    }

    public function getName(): string
    {
        // Nutze die UID für den Dateinamen mit .vcf-Endung
        return $this->contact->getUid() . '.vcf';
    }

    public function get()
    {
        $vcard = $this->generateVCard();
        return $vcard;
    }

    public function getSize()
    {
        return strlen($this->generateVCard());
    }

    public function getETag()
    {
        return '"' . md5($this->generateVCard()) . '"';
    }

    private function generateVCard(): string
    {
        $firstName = $this->contact->getFirstName() ?? '';
        $lastName = $this->contact->getLastName() ?? '';
        $fullName = trim("$firstName $lastName");

        $email = $this->contact->getEmail();
        $phone = $this->contact->getPhone();
        $address = $this->contact->getAddress();
        $notes = $this->contact->getNotes();

        $uid = $this->contact->getUid() ?? uniqid();

        $vcardLines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            'FN:' . $fullName,
            'N:' . $lastName . ';' . $firstName . ';;;',
            'UID:' . $uid,
        ];

        if ($email) {
            $vcardLines[] = 'EMAIL;TYPE=INTERNET:' . $email;
        }

        if ($phone) {
            $vcardLines[] = 'TEL;TYPE=CELL:' . $phone;
        }

        if ($address) {
            // Einfach als Label-Adresse (kann man noch besser strukturieren)
            $vcardLines[] = 'ADR;TYPE=HOME:;;' . $address . ';;;;';
        }

        if ($notes) {
            $vcardLines[] = 'NOTE:' . $notes;
        }

        $vcardLines[] = 'END:VCARD';

        return implode("\r\n", $vcardLines) . "\r\n";
    }
}
