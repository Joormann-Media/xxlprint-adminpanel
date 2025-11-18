<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestApiController extends AbstractController
{
    /**
 * @OA\Info(
 *     title="XXL-Print API",
 *     version="1.0.0",
 *     description="Dokumentation der API für das Admin-Interface"
 * )
 * @OA\Server(
 *     url="https://admin.xxl-print-wesel.de",
 *     description="Production Server"
 * )
 */

    #[Route('/api/test', methods: ['POST'])]
    public function test(): JsonResponse
    {
        return new JsonResponse(['message' => 'Test erfolgreich']);
    }
    
}
