<?php
// src/Controller/Api/AuthValidationController.php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;


#[Route('/api-auth', name: 'api_auth')]
class AuthValidationController extends AbstractController
{    
/**
 * Controller for validating user authentication via PIN and password.
 *
 * This controller provides an API endpoint to validate a user's PIN and password.
 * It checks if the provided credentials match those stored in the database.
 * - If both PIN and password are valid, it returns a success response.
 * - If either is invalid, it returns an error response indicating which credential failed.
 * - If the user is not found, it returns a 404 error.
 *
 * This controller is designed to be used in a Symfony application
 * and expects the request body to be in JSON format with the following structure:
 * {
 *   "email":"name@domain.tld",
 *   "pin":"123456",
 *   "password":"your_password"
 * }
 

 */

    #[Route('/validate-auth', name: 'validate_auth', methods: ['POST'])]
   
    public function validate(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Ungültiges JSON-Format.'], 400);
        }
    
        $email = $data['email'] ?? null;
        if (!$email) {
            return new JsonResponse(['error' => 'E-Mail-Adresse fehlt.'], 400);
        }
    
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
    
        if (!$user) {
            return new JsonResponse(['error' => 'Benutzer nicht gefunden.'], 404);
        }
    
        $pinValid = null;
        $passwordValid = null;
    
        // PIN prüfen
        if (isset($data['pin'])) {
            $pinValid = password_verify($data['pin'], $user->getUserPin());
        }
        
    
        // Passwort prüfen
        if (isset($data['password'])) {
            $passwordValid = $hasher->isPasswordValid($user, $data['password']);
        }
    
        $allValid = true;
    
        if (isset($pinValid)) {
            $allValid = $allValid && $pinValid;
        }
    
        if (isset($passwordValid)) {
            $allValid = $allValid && $passwordValid;
        }
    
        return new JsonResponse([
            'pinValid' => $pinValid,
            'passwordValid' => $passwordValid,
            'valid' => $allValid,
        ]);
    }

}
