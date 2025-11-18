<?php

namespace App\Controller;

use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Repository\UserProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user-profile')]
final class UserProfileController extends AbstractController
{
    // 🛡️ Helper-Funktion zur Rollenprüfung
    private function isUserAdmin(): bool
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            return false;
        }

        return in_array('ROLE_USERADMIN', $user->getRoles(), true);
    }

    // 🌟 Intelligente Startseite: Admin → Index, User → eigenes Profil
    #[Route(name: 'app_user_profile_index_or_show', methods: ['GET'])]
    public function indexOrShow(UserProfileRepository $userProfileRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Kein Benutzer eingeloggt.');
        }

        if ($this->isUserAdmin()) {
            return $this->render('user_profile/index.html.twig', [
                'user_profiles' => $userProfileRepository->findAll(),
                'page_title' => 'Benutzerprofile verwalten',
            ]);
        }

        $profile = $user->getProfile();
        if (!$profile) {
            throw $this->createNotFoundException('Kein Profil vorhanden.');
        }

        return $this->redirectToRoute('app_user_profile_show', [
            'id' => $profile->getId(),
        ]);
    }

    // 🆕 Neues Profil anlegen
    #[Route('/new', name: 'app_user_profile_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userProfile = new UserProfile();
        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userProfile);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_profile_index_or_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_profile/new.html.twig', [
            'user_profile' => $userProfile,
            'form' => $form,
        ]);
    }

    // 🔎 Einzelnes Profil anzeigen
    #[Route('/{id<\d+>}', name: 'app_user_profile_show', methods: ['GET'])]
    public function show(int $id, UserProfileRepository $userProfileRepository): Response
    {
        $userProfile = $userProfileRepository->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$userProfile) {
            throw $this->createNotFoundException('Profil nicht gefunden.');
        }

        return $this->render('user_profile/show.html.twig', [
            'user_profile' => $userProfile,
            'user' => $userProfile->getUser(),
            'page_title' => 'Profil ansehen',
        ]);
    }

    // ✏️ Profil bearbeiten
    #[Route('/{id<\d+>}/edit', name: 'app_user_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, UserProfileRepository $userProfileRepository, EntityManagerInterface $entityManager): Response
    {
        $userProfile = $userProfileRepository->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')
            ->addSelect('u')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$userProfile) {
            throw $this->createNotFoundException('Profil nicht gefunden.');
        }

        $user = $userProfile->getUser();
        if (!$user) {
            throw new \LogicException('Kein Benutzer mit diesem Profil verknüpft.');
        }

        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        // Avatar-Handling
        if ($form->isSubmitted()) {
            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $userDir = $user->getUserDir() ?: bin2hex(random_bytes(6));
                $user->setUserDir($userDir);

                $uploadDir = $this->getParameter('user_data_base_path') . '/' . $userDir . '/avatar';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $newFilename = uniqid().'.'.$avatarFile->guessExtension();
                $avatarFile->move($uploadDir, $newFilename);

                $user->setAvatar($newFilename);
            }

            if ($form->get('removeAvatar')->getData()) {
                if ($user->getAvatar()) {
                    $avatarPath = $this->getParameter('user_data_base_path') . '/' . $user->getUserDir() . '/avatar/' . $user->getAvatar();
                    if (file_exists($avatarPath)) {
                        @unlink($avatarPath);
                    }
                    $user->setAvatar(null);
                }
            }

            if ($form->isValid()) {
                $entityManager->flush();
                return $this->redirectToRoute('app_user_profile_index_or_show', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('user_profile/edit.html.twig', [
            'user_profile' => $userProfile,
            'user' => $user,
            'form' => $form,
            'page_title' => 'Profil bearbeiten',
        ]);
    }

    // 🗑️ Profil löschen
    #[Route('/{id<\d+>}', name: 'app_user_profile_delete', methods: ['POST'])]
    public function delete(Request $request, UserProfile $userProfile, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userProfile->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userProfile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_profile_index_or_show', [], Response::HTTP_SEE_OTHER);
    }
}
