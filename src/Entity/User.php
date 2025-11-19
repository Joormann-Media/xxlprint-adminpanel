<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use App\Entity\Permission; // Added missing use statement
use App\Entity\UserProfile; // Added missing use statement
use App\Entity\UserDevice; // Added missing use statement
use App\Entity\UserDashboardConfig; // Added missing use statement
use App\Entity\Documentations; // Added missing use statement

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    // Changed to nullable array to prevent "accessed before initialization" error
    #[ORM\Column(type: Types::JSON, nullable: true)] 
    private ?array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $regDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastlogindate = null;

    #[ORM\Column(length: 255)]
    private ?string $userpin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prename = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $usergroups = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $passwordChangedAt = null; 
    // Letzte Passwortänderung

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $failedAttempts = null; 
    // Anzahl der fehlgeschlagenen Login-Versuche

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $isLocked = null; 
    // Ob der Benutzer nach zu vielen Fehlversuchen gesperrt wurde

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $isActive = null; 
    // Ob der Benutzer aktiv ist

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $twoFactorSecret = null; 
    // Geheimnis für Zwei-Faktor-Authentifizierung (2FA)

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $isTwoFactorEnabled = null; 
    // Ob der Benutzer 2FA aktiviert hat

    /**
     * @var Collection<int, Permission>
     */
    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'users', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'user_permission')]
    private Collection $permissions;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(nullable: true)]
    private ?bool $adminOverride = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminOverrideId = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?UserProfile $profile = null;

    /**
     * @var Collection<int, UserDevice>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserDevice::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $devices;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userDir = null;

#[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['default' => '5'])]
private ?string $maxDevice = '5';


    /**
     * @var Collection<int, UserDashboardConfig>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserDashboardConfig::class, orphanRemoval: true)]
    private Collection $dashboardConfigs;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ldapSyncedAt = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    private ?string $emailVerificationToken = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $mobile = null;

    #[ORM\Column(nullable: true)]
    private ?bool $mobileVerified = null;

    // Setter parameter type changed to string (non-nullable) to match column definition
    #[ORM\Column(length: 255)]
    private ?string $customerId = null;
    
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $digestHash = null;

    /**
     * @var Collection<int, Documentations>
     */
    #[ORM\OneToMany(mappedBy: 'docuMaintainer', targetEntity: Documentations::class)]
    private Collection $documentations;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: UserOnlineStatus::class, cascade: ['persist', 'remove'])]
    private ?UserOnlineStatus $onlineStatus = null;

    #[ORM\ManyToOne(targetEntity: Language::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Language $language = null;


    public function __construct()
    {
        $this->roles = []; // Initialize to empty array for new objects
        $this->usergroups = [];
        $this->permissions = new ArrayCollection();
        $this->devices = new ArrayCollection();
        $this->dashboardConfigs = new ArrayCollection();
        $this->documentations = new ArrayCollection();
        $this->maxDevice = 5; // Ensure default value is set for new entities
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        // Ensure $this->roles is an array, even if it was hydrated as null from the database
        $roles = $this->roles ?? []; 

        // Ensure every user has at least ROLE_USER
        $roles[] = 'ROLE_USER';

        $cleanedRoles = [];
        foreach ($roles as $role) {
            // Split by comma and trim each part, then add to cleanedRoles
            // This handles cases where a single string like "ROLE_ADMIN,ROLE_EDITOR" might be stored
            // within the JSON array, although ideally the JSON should store ["ROLE_ADMIN", "ROLE_EDITOR"]
            $parts = array_map('trim', explode(',', $role));
            foreach ($parts as $part) {
                if ($part !== '') { // Avoid empty strings
                    $cleanedRoles[] = strtoupper($part); // Ensure uppercase for consistency
                }
            }
        }

        // Remove duplicates and re-index the array
        return array_values(array_unique($cleanedRoles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getRegDate(): ?\DateTimeInterface
    {
        return $this->regDate;
    }

    public function setRegDate(\DateTimeInterface $regDate): static
    {
        $this->regDate = $regDate;

        return $this;
    }

    public function getLastlogindate(): ?\DateTimeInterface
    {
        return $this->lastlogindate;
    }

    public function setLastlogindate(\DateTimeInterface $lastlogindate): static
    {
        $this->lastlogindate = $lastlogindate;

        return $this;
    }

    public function getUserpin(): ?string
    {
        return $this->userpin;
    }

    public function setUserpin(string $userpin): static
    {
        $this->userpin = $userpin;

        return $this;
    }

    public function getPrename(): ?string
    {
        return $this->prename;
    }

    public function setPrename(?string $prename): static
    {
        $this->prename = $prename;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUsergroups(): ?array
    {
        return $this->usergroups;
    }
    
    public function setUsergroups(?array $usergroups): static
    {
        $this->usergroups = $usergroups;
        return $this;
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): static
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
            $permission->addUser($this); // Ensure the inverse side is set
        }

        return $this;
    }

    public function removePermission(Permission $permission): static
    {
        if ($this->permissions->removeElement($permission)) {
            $permission->removeUser($this); // Ensure the inverse side is unset
        }

        return $this;
    }

    // Neue Methoden
    public function getPasswordChangedAt(): ?\DateTimeInterface
    {
        return $this->passwordChangedAt;
    }

    public function getFailedAttempts(): ?int
    {
        return $this->failedAttempts;
    }

    public function isLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function getTwoFactorSecret(): ?string
    {
        return $this->twoFactorSecret;
    }

    public function isTwoFactorEnabled(): ?bool
    {
        return $this->isTwoFactorEnabled;
    }

    public function setPasswordChangedAt(?\DateTimeInterface $passwordChangedAt): static
    {
        $this->passwordChangedAt = $passwordChangedAt;
        return $this;
    }

    public function setFailedAttempts(?int $failedAttempts): static
    {
        $this->failedAttempts = $failedAttempts;
        return $this;
    }

    public function setIsLocked(?bool $isLocked): static
    {
        $this->isLocked = $isLocked;
        return $this;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function setTwoFactorSecret(?string $twoFactorSecret): static
    {
        $this->twoFactorSecret = $twoFactorSecret;
        return $this;
    }

    public function setIsTwoFactorEnabled(?bool $isTwoFactorEnabled): static
    {
        $this->isTwoFactorEnabled = $isTwoFactorEnabled;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function isAdminOverride(): ?bool
    {
        return $this->adminOverride;
    }

    public function setAdminOverride(?bool $adminOverride): static
    {
        $this->adminOverride = $adminOverride;
        return $this;
    }

    public function getAdminOverrideId(): ?string
    {
        return $this->adminOverrideId;
    }
    
    public function setAdminOverrideId(?string $adminOverrideId): static
    {
        $this->adminOverrideId = $adminOverrideId;
        return $this;
    }

    public function getProfile(): ?UserProfile
    {
        return $this->profile;
    }

    public function setProfile(?UserProfile $profile): static
    {
        // set the owning side of the relation if necessary
        if ($profile !== null && $profile->getUser() !== $this) {
            $profile->setUser($this);
        }

        $this->profile = $profile;
        return $this;
    }

    /**
     * @return Collection<int, UserDevice>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(UserDevice $device): static
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
            $device->setUser($this);
        }

        return $this;
    }

    public function removeDevice(UserDevice $device): static
    {
        if ($this->devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getUser() === $this) {
                $device->setUser(null);
            }
        }

        return $this;
    }

    public function getUserDir(): ?string
    {
        return $this->userDir;
    }

    public function setUserDir(?string $userDir): static
    {
        $this->userDir = $userDir;
        return $this;
    }

    // Changed type to ?int
    public function getMaxDevice(): ?int
    {
        return $this->maxDevice;
    }

    // Changed type to ?int
    public function setMaxDevice(?int $maxDevice): static
    {
        $this->maxDevice = $maxDevice;
        return $this;
    }

    /**
     * @return Collection<int, UserDashboardConfig>
     */
    public function getDashboardConfigs(): Collection
    {
        return $this->dashboardConfigs;
    }

    public function addDashboardConfig(UserDashboardConfig $dashboardConfig): static
    {
        if (!$this->dashboardConfigs->contains($dashboardConfig)) {
            $this->dashboardConfigs->add($dashboardConfig);
            $dashboardConfig->setUser($this);
        }

        return $this;
    }

    public function removeDashboardConfig(UserDashboardConfig $dashboardConfig): static
    {
        if ($this->dashboardConfigs->removeElement($dashboardConfig)) {
            // set the owning side to null (unless already changed)
            if ($dashboardConfig->getUser() === $this) {
                $dashboardConfig->setUser(null);
            }
        }

        return $this;
    }

    public function getLdapSyncedAt(): ?\DateTimeInterface
    {
        return $this->ldapSyncedAt;
    }

    public function setLdapSyncedAt(?\DateTimeInterface $ldapSyncedAt): static
    {
        $this->ldapSyncedAt = $ldapSyncedAt;
        return $this;
    }

    public function getEmailVerificationToken(): ?string
    {
        return $this->emailVerificationToken;
    }

    public function setEmailVerificationToken(?string $emailVerificationToken): static
    {
        $this->emailVerificationToken = $emailVerificationToken;
        return $this;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }
        return $this->getId() === $user->getId();
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;
        return $this;
    }

    public function isMobileVerified(): ?bool
    {
        return $this->mobileVerified;
    }

    public function setMobileVerified(?bool $mobileVerified): static
    {
        $this->mobileVerified = $mobileVerified;
        return $this;
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    // Changed parameter type to string (non-nullable)
    public function setCustomerId(string $customerId): static
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getDigestHash(): ?string
    {
        return $this->digestHash;
    }

    public function setDigestHash(?string $digestHash): static
    {
        $this->digestHash = $digestHash;
        return $this;
    }

    /**
     * @return Collection<int, Documentations>
     */
    public function getDocumentations(): Collection
    {
        return $this->documentations;
    }

    public function addDocumentation(Documentations $documentation): static
    {
        if (!$this->documentations->contains($documentation)) {
            $this->documentations->add($documentation);
            $documentation->setDocuMaintainer($this);
        }

        return $this;
    }

    public function removeDocumentation(Documentations $documentation): static
    {
        if ($this->documentations->removeElement($documentation)) {
            // set the owning side to null (unless already changed)
            if ($documentation->getDocuMaintainer() === $this) {
                $documentation->setDocuMaintainer(null);
            }
        }

        return $this;
    }

    
public function getFullName(): string
{
    return trim(($this->prename ?? '') . ' ' . ($this->name ?? ''));
}

public function getOnlineStatus(): ?UserOnlineStatus
{
    return $this->onlineStatus;
}

public function setOnlineStatus(?UserOnlineStatus $onlineStatus): static
{
    // unset the owning side of the relation if necessary
    if ($onlineStatus === null && $this->onlineStatus !== null) {
        $this->onlineStatus->setUser(null);
    }

    // set the owning side of the relation if necessary
    if ($onlineStatus !== null && $onlineStatus->getUser() !== $this) {
        $onlineStatus->setUser($this);
    }

    $this->onlineStatus = $onlineStatus;

    return $this;
}
public function __toString(): string
{
    return $this->getFullName()
        ?: $this->getUsername()
        ?: $this->getEmail()
        ?: 'User#' . $this->getId();
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