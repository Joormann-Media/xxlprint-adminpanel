<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user', name: 'app_user_api_')]
final class UserApiController extends AbstractController
{
    #[Route('/update', name: 'update', methods: ['POST'])]
    //#[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function updateField(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $field = $data['field'] ?? null;
        $value = $data['value'] ?? null;

        /** @var User $user */
        $user = $this->getUser();

        if (!$field || $value === null) {
            return $this->json(['error' => 'Feld und Wert müssen angegeben werden.'], 400);
        }

        try {
            switch ($field) {
                case 'lastlogindate':
                    $user->setLastlogindate(new \DateTime($value));
                    break;
                case 'userpin':
                    $user->setUserpin($value);
                    break;
                case 'isActive':
                    $user->setIsActive((bool) $value);
                    break;
                // Hier kannst du weitere Felder ergänzen
                default:
                    return $this->json(['error' => 'Ungültiges Feld'], 400);
            }

            $em->flush();

            return $this->json(['success' => true]);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

#[Route('/{id<\d+>}', name: 'get_user_data', methods: ['GET'])]
public function getUserData(int $id, EntityManagerInterface $em): JsonResponse
{
    $user = $em->getRepository(User::class)->find($id);

    if (!$user) {
        return $this->json(['error' => 'User not found'], 404);
    }

    return $this->json([
        'id' => $user->getId(),
        'email' => $user->getEmail(),
        'prename' => $user->getPrename(),
        'name' => $user->getName(),
        'mobile' => $user->getMobile(),
    ]);
}

}
