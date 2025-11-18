<?php
// src/Controller/Api/SwaggerTestController.php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class SwaggerTestController extends AbstractController
{
    /**
     * Controller for testing Swagger documentation generation.     
     * This controller is used to test Swagger documentation generation.
     * It provides a simple POST endpoint that accepts a JSON body with a "test" property.
     * The endpoint is defined with OpenAPI annotations to ensure it is documented correctly.
     * 
     * @OA\Info(
     *    title="Swagger Test API",
     *   version="1.0.0",
     *   description="API for testing Swagger documentation generation"
     * )
     * @OA\Path(
     *    path="/api/swagger-test",
     *   summary="Test endpoint for Swagger documentation",
     *   @OA\Tag(name="Swagger Test")
     * )
     *         #[OA\Get(path: '/api/swagger-test', summary: 'Test endpoint for Swagger documentation')]
    * #[OA\Response(response: 200, description: 'AOK')]
    *  #[OA\Response(response: 401, description: 'Not allowed')]
     * 
     * 
     * @OA\Post(
     *     path="/api/swagger-test",
     *     summary="Swagger Test",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"test"},
     *             @OA\Property(property="test", type="string", example="hello")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Test OK"
     *     )
     * )
     */
    #[Route('/api/swagger-test', name: 'swagger_test', methods: ['POST'])]

    public function test(): JsonResponse
    {
        return new JsonResponse(['ok' => true]);
    }
}
