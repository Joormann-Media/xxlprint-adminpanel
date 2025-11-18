<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\UserHistory;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Passwort & PIN hashen
            $plainPassword = $form->get('plainPassword')->getData();
            $plainPin = $form->get('userpin')->getData();

            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $user->setUserPin($passwordHasher->hashPassword($user, $plainPin));

            // E-Mail zufällig generieren (anstatt aus Formular)
            $email = bin2hex(random_bytes(8)) . '@example.local';
            $user->setEmail($email);

            // Customer-Id setzen
            $customerIdPrefix = 'CUST-';
            $customerId = $customerIdPrefix . date('Ym') . mt_rand(1000, 9999);
            $user->setCustomerId($customerId);

            // Rollen & Pfad setzen
            $user->setRoles(['ROLE_USER', 'ROLE_CUSTOMER']);
            $userDir = bin2hex(random_bytes(6));
            $user->setUserDir($userDir);
            $user->setUsergroups(['9']);
            $user->setRegDate(new \DateTime());
            $user->setLastLogindate(new \DateTime());

            // Benutzerverzeichnisse erstellen
            $fs = new Filesystem();
            $baseDir = $this->getParameter('kernel.project_dir') . '/public/user_data/' . $userDir;
            $fs->mkdir([
                $baseDir . '/private',
                $baseDir . '/public',
                $baseDir . '/avatar',
            ]);

            // Benutzerprofil anlegen
            $profile = new UserProfile();
            $profile->setUser($user);
            $profile->setMotto('Neue Registrierung');

            // History-Eintrag erzeugen (optional, wie gehabt)
            $history = new UserHistory();
            $history->setUser($user);
            $history->setIpAddress($request->getClientIp());
            $history->setDevice($request->headers->get('User-Agent'));
            $history->setAction('Registrierung abgeschlossen');



            // Metadaten mergen (wie gehabt)
          
            $history->setMetaData(array_merge([
                'user_dir' => $userDir,
                'avatar' => $user->getAvatar(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
                'name' => $user->getName(),
                'prename' => $user->getPrename(),
                'usergroups' => $user->getUsergroups(),
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
                'last_login' => new \DateTime(),
                'last_activity' => new \DateTime(),
                'last_password_change' => new \DateTime(),
                'last_pin_change' => new \DateTime(),
                'last_avatar_change' => new \DateTime(),
                'last_profile_change' => new \DateTime(),
                'last_history_change' => new \DateTime(),
                'last_login_ip' => $request->getClientIp(),
            ], ));
            $history->setTimestamp(new \DateTime());

            // Speichern
            $entityManager->persist($user);
            $entityManager->persist($profile);
            $entityManager->persist($history);
            $entityManager->flush();

            $this->addFlash('success', 'Registrierung erfolgreich!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'page_title' => 'Benutzer registrieren',
            'form' => $form->createView(),
        ]);
    }
}
