<?php

namespace App\Controller\Api;

use App\Entity\Permission;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Annotations as OA;

#[Route('/api/permission')]
final class PermissionsApiController extends AbstractController
{
    public function __construct(
        private PermissionRepository $permissionRepository,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * @OA\Post(
     *     path="/api/permission/add",
     *     summary="Fügt eine neue Berechtigung hinzu",
     *     tags={"Permission"},
     *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"name", "permissionRoute", "minRole"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="createBy", type="string"),
 *             @OA\Property(property="permissionRoute", type="string"),
 *             @OA\Property(property="isActive", type="boolean"),
 *             @OA\Property(property="onMobileIOS", type="boolean"),
 *             @OA\Property(property="onMobileAndroid", type="boolean"),
 *             @OA\Property(property="onOtherMobile", type="boolean"),
 *             @OA\Property(property="onChromeOS", type="boolean"),
 *             @OA\Property(property="onWindows", type="boolean"),
 *             @OA\Property(property="onLinux", type="boolean"),
 *             @OA\Property(property="onMacOS", type="boolean"),
 *             @OA\Property(property="allowedCountries", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="blockedCountries", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="minRole", type="string"),
 *             @OA\Property(property="pinRequired", type="boolean")
 *         )
 *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Erfolgreiche Erstellung",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permission successfully added"),
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ungültige JSON-Daten",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Invalid JSON data"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Interner Serverfehler",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Failed to add permission"))
     *     )
     * )
     */
    #[Route('/add', name: 'app_permission_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $permission = new Permission();
            $permission->setName($data['name'] ?? null);
            $permission->setDescription($data['description'] ?? null);
            $permission->setCreatedate(new \DateTime());
            $permission->setCreateBy($data['createBy'] ?? 'System');
            $permission->setPermissionRoute($data['permissionRoute'] ?? null);
            $permission->setIsActive($data['isActive'] ?? true);
            $permission->setOnMobileIOS($data['onMobileIOS'] ?? true);
            $permission->setOnMobileAndroid($data['onMobileAndroid'] ?? true);
            $permission->setOnOtherMobile($data['onOtherMobile'] ?? true);
            $permission->setOnChromeOS($data['onChromeOS'] ?? true);
            $permission->setOnWindows($data['onWindows'] ?? true);
            $permission->setOnLinux($data['onLinux'] ?? true);
            $permission->setOnMacOS($data['onMacOS'] ?? true);
            $permission->setAllowedCountries($data['allowedCountries'] ?? []);
            $permission->setBlockedCountries($data['blockedCountries'] ?? []);
            $permission->setMinRole($data['minRole'] ?? null);
            $permission->setPinRequired($data['pinRequired'] ?? false);

            $this->entityManager->persist($permission);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Permission successfully added',
                'id' => $permission->getId()
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to add permission: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
