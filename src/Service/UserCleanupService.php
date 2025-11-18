<?php

// src/Service/UserCleanupService.php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\UserPermission;
use App\Entity\UserDevice;
use App\Entity\UserHistory;
use App\Entity\UserGroups;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;

class UserCleanupService
{
    private Filesystem $filesystem;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly string $userDataBasePath,
    ) {
        $this->filesystem = new Filesystem();
    }

    public function deleteUserAndRelatedData(User $user): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'log' => [],
        ];

        try {
            $userId = $user->getId();

            // Profile
            $profile = $this->entityManager->getRepository(UserProfile::class)->findOneBy(['user' => $user]);
            if ($profile) {
                $this->entityManager->remove($profile);
                $result['log'][] = "✅ UserProfile entfernt.";
            } else {
                $result['log'][] = "ℹ️ Kein UserProfile gefunden.";
            }

            // Permissions
            $permissions = $user->getPermissions(); // <- wenn korrekt mapped

            foreach ($permissions as $permission) {
                $user->removePermission($permission); // Wenn du eine remove-Methode hast
            }
            $result['log'][] = count($permissions) . " UserPermissions-Verknüpfungen entfernt.";

            // Devices
            $devices = $this->entityManager->getRepository(UserDevice::class)->findBy(['user' => $user]);
            foreach ($devices as $d) {
                $this->entityManager->remove($d);
            }
            $result['log'][] = "✅ " . count($devices) . " UserDevices entfernt.";

            // History
            $history = $this->entityManager->getRepository(UserHistory::class)->findBy(['user' => $user]);
            foreach ($history as $h) {
                $this->entityManager->remove($h);
            }
            $result['log'][] = "✅ " . count($history) . " UserHistory-Einträge entfernt.";

            // UserGroups (wenn userId in JSON gespeichert ist)
            $groups = $this->entityManager->getRepository(UserGroups::class)->findBy(['groupMembers' => $userId]);
            foreach ($groups as $g) {
                $this->entityManager->remove($g);
            }
            $result['log'][] = "✅ " . count($groups) . " UserGroup-Zuordnungen entfernt.";

            // User selbst löschen
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            $result['log'][] = "✅ Benutzer entfernt.";

            // Verzeichnis löschen
            $userDir = $user->getUserDir();
            if ($userDir) {
                $dir = $this->userDataBasePath . '/' . $userDir;
                if ($this->filesystem->exists($dir)) {
                    $this->filesystem->remove($dir);
                    $result['log'][] = "🧹 Verzeichnis '$dir' gelöscht.";
                } else {
                    $result['log'][] = "ℹ️ Verzeichnis '$dir' existiert nicht.";
                }
            }

            $result['success'] = true;
            $result['message'] = "Benutzer erfolgreich gelöscht.";
        } catch (\Throwable $e) {
            $result['message'] = "Fehler: " . $e->getMessage();
            $result['log'][] = "❌ Exception: " . $e->getMessage();
        }

        

        return $result;
    }
    
}
