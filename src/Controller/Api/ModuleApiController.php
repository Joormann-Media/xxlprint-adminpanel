<?php

namespace App\Controller\Api;

use App\Entity\Module;
use App\Repository\ModuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/module', name: 'api_module')]
final class ModuleApiController extends AbstractController
{
    #[Route('/', name: 'get_all_modules', methods: ['GET'])]
    public function getAllModules(ModuleRepository $moduleRepository): JsonResponse
    {
        $modules = $moduleRepository->findAll();

        if (!$modules) {
            return new JsonResponse(['error' => 'Keine Module gefunden'], 404);
        }

        $sanitize = fn($value) => ($value === null || $value === '') ? 'FOLGT' : $value;

        $data = [];

        foreach ($modules as $module) {
            $data[] = [
                'id'              => $module->getId(),
                'modul_id'        => $sanitize($module->getModulId()),
                'name'            => $sanitize($module->getName()),
                'is_active'       => $module->isActive(),
                'is_maintenance'  => $module->isInMaintenance(),
                'min_role'        => $sanitize($module->getMinRole()),
                'last_update'     => $module->getLastUpdate()?->format('Y-m-d H:i:s'),
                'last_username'   => $sanitize($module->getLastUsername()),
                'last_reason'     => $sanitize($module->getLastReason()),
                'active_from'     => $module->getActiveFrom()?->format('Y-m-d H:i:s'),
                'active_until'    => $module->getActiveUntil()?->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data, 200);
    }
}
