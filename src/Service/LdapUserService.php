<?php
// src/Service/LdapUserService.php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\LdapException;
use Psr\Log\LoggerInterface;

class LdapUserService
{
    private string $baseDn = 'ou=users,dc=admin,dc=joormann,dc=media,dc=de';

    public function __construct(
        private readonly LdapInterface $ldap,
        private readonly LoggerInterface $logger,
        private readonly string $ldapBindDn,
        private readonly string $ldapPassword,
    ) {
        // Removed redundant initialization
    }

    public function syncUser(User $user): void
    {
        $dn = "uid={$user->getUsername()}," . $this->baseDn;

        $attributes = [
            'objectClass' => ['inetOrgPerson', 'organizationalPerson', 'person', 'top'],
            'cn' => [$user->getPrename() . ' ' . $user->getName()],
            'sn' => [$user->getName()],
            'givenName' => [$user->getPrename()],
            'uid' => [$user->getUsername()],
            'mail' => [$user->getEmail()],
        ];

        if ($user->getPassword()) {
            $attributes['userPassword'] = [$user->getPassword()];
        }

        $profile = $user->getProfile();
        if ($profile) {
            if ($profile->getPhonePrivate()) {
                $attributes['telephoneNumber'] = [$profile->getPhonePrivate()];
            }
            if ($profile->getPhoneMobile()) {
                $attributes['mobile'] = [$profile->getPhoneMobile()];
            }
            if ($profile->getStreet()) {
                $attributes['street'] = [$profile->getStreet()];
            }
            if ($profile->getPostalCode()) {
                $attributes['postalCode'] = [$profile->getPostalCode()];
            }
            if ($profile->getCity()) {
                $attributes['l'] = [$profile->getCity()];
            }
            if ($profile->getCountry()) {
                $attributes['c'] = [$profile->getCountry()];
            }
        }

        try {
            $this->logger->info("Attempting to bind to LDAP server with DN: {$this->ldapBindDn}");
            $this->ldap->bind($this->ldapBindDn, $this->ldapPassword); // Explicit bind with credentials
            $this->logger->info("Successfully connected to LDAP server.");

            if ($this->userExists($user)) {
                $this->logger->info("LDAP: User {$user->getUsername()} exists – updating.");
                $this->ldap->getEntryManager()->update(new Entry($dn, $attributes));
            } else {
                $this->logger->info("LDAP: User {$user->getUsername()} does not exist – creating.");
                $this->ldap->getEntryManager()->add(new Entry($dn, $attributes));
            }

            $user->setLdapSyncedAt(new \DateTime());
        } catch (LdapException $e) {
            $this->logger->error("LDAP-Sync failed for {$user->getUsername()}: " . $e->getMessage());
            $this->logger->error("Ensure the LDAP server is reachable and the credentials are correct.");
            throw $e;
        } catch (\Exception $e) {
            $this->logger->critical("Unexpected error during LDAP operation: " . $e->getMessage());
            throw $e;
        }
    }

    public function userExists(User $user): bool
    {
        try {
            $this->logger->info("Checking if user {$user->getUsername()} exists in LDAP.");
            $this->ldap->bind($this->ldapBindDn, $this->ldapPassword); // Explicit bind
            $results = $this->ldap->query($this->baseDn, "(uid={$user->getUsername()})")->execute();
            $exists = count($results->toArray()) > 0;
            $this->logger->info("LDAP userExists check for {$user->getUsername()}: " . ($exists ? "found" : "not found"));
            return $exists;
        } catch (LdapException $e) {
            $this->logger->warning("LDAP userExists() failed for {$user->getUsername()}: " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->logger->critical("Unexpected error during userExists() check: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser(User $user): void
    {
        $dn = "uid={$user->getUsername()}," . $this->baseDn;

        $attributes = [
            'cn' => [$user->getPrename() . ' ' . $user->getName()],
            'sn' => [$user->getName()],
            'givenName' => [$user->getPrename()],
            'mail' => [$user->getEmail()],
            'description' => [$user->getProfile()?->getBirthdate()?->format('Y-m-d') ?? ''],
        ];

        if ($user->getPassword()) {
            $attributes['userPassword'] = [$user->getPassword()];
        }

        $profile = $user->getProfile();
        if ($profile) {
            if ($profile->getPhonePrivate()) {
                $attributes['telephoneNumber'] = [$profile->getPhonePrivate()];
            }
            if ($profile->getPhoneMobile()) {
                $attributes['mobile'] = [$profile->getPhoneMobile()];
            }
            if ($profile->getStreet()) {
                $attributes['street'] = [$profile->getStreet()];
            }
            if ($profile->getPostalCode()) {
                $attributes['postalCode'] = [$profile->getPostalCode()];
            }
            if ($profile->getCity()) {
                $attributes['l'] = [$profile->getCity()];
            }
            if ($profile->getCountry()) {
                $attributes['c'] = [$profile->getCountry()];
            }
        }

        try {
            $this->ldap->bind($this->ldapBindDn, $this->ldapPassword);

            $results = $this->ldap->query($this->baseDn, "(uid={$user->getUsername()})")->execute();
            $entries = $results->toArray();

            if (count($entries) === 0) {
                throw new \Exception("LDAP entry for {$user->getUsername()} not found.");
            }

            $entry = $entries[0];
            foreach ($attributes as $key => $value) {
                $entry->setAttribute($key, $value);
            }

            $this->ldap->getEntryManager()->update($entry);
            $user->setLdapSyncedAt(new \DateTime());
        } catch (\Exception $e) {
            $this->logger->error("LDAP-Update failed for {$user->getUsername()}: " . $e->getMessage());
            throw $e;
        }
    }
    public function deleteUser(User $user): void
{
    $dn = "uid={$user->getUsername()},ou=users,dc=admin,dc=joormann,dc=media,dc=de";
    $this->ldap->bind($this->ldapBindDn, $this->ldapPassword);
    $this->ldap->getEntryManager()->remove(new Entry($dn));
}

}
