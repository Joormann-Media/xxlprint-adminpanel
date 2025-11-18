<?php

namespace App\Service;

use App\Repository\ModuleRepository;

class ModuleManagerService
{
    public function __construct(private readonly ModuleRepository $moduleRepo) {}

    public function isModuleActive(string $modulId): bool
    {
        $module = $this->moduleRepo->findOneBy(['modulId' => $modulId]);

        if (!$module) return false;

        $now = new \DateTime();
        if ($module->getActiveFrom() && $module->getActiveFrom() > $now) return false;
        if ($module->getActiveUntil() && $module->getActiveUntil() < $now) return false;

        return $module->isActive() && !$module->isInMaintenance();
    }

    public function isInMaintenance(string $modulId): bool
    {
        $module = $this->moduleRepo->findOneBy(['modulId' => $modulId]);
        return $module ? $module->isInMaintenance() : false;
    }

    public function getMinRole(string $modulId): ?string
    {
        $module = $this->moduleRepo->findOneBy(['modulId' => $modulId]);
        return $module?->getMinRole();
    }
}
