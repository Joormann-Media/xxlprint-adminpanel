<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SetupController extends AbstractController
{
    #[Route('/setup', name: 'app_setup', methods: ['GET', 'POST'])]
    public function setup(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {

        $lockFile = dirname(__DIR__, 2) . '/var/setup.lock';

        // Wenn Setup bereits ausgeführt
        if (file_exists($lockFile)) {
            return $this->render('setup/already_done.html.twig');
        }

        // Wenn bereits User existieren → auch sperren
        if ($em->getRepository(User::class)->count([]) > 0) {
            file_put_contents($lockFile, "auto-lock");
            return $this->render('setup/already_done.html.twig');
        }

        // Formular abgeschickt?
        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');
            $username = $request->request->get('username');
            $prename = $request->request->get('prename');
            $name = $request->request->get('name');
            $password = $request->request->get('password');
            $pin = $request->request->get('pin');

            if (!$email || !$username || !$password || !$pin) {
                $this->addFlash('danger', 'Bitte alle Pflichtfelder ausfüllen.');
                return $this->redirectToRoute('app_setup');
            }

            // -------------------------
            // 1. Rollen anlegen
            // -------------------------
            $roleDefs = [
                [1,  'ROLE_SUPREME_ADMIN',  'Supreme Administrator (volle Kontrolle)', 'ROLE_SUPREME_ADMIN'],
                [2,  'ROLE_SYSADMIN',       'Systemadministrator',                      'ROLE_SYSADMIN'],
                [3,  'ROLE_ADMIN',          'Administrator',                             'ROLE_ADMIN'],
                [4,  'ROLE_USERADMIN',      'Benutzerverwaltung',                        'ROLE_USERADMIN'],
                [5,  'ROLE_RELEASEMANAGER', 'Release-Manager',                           'ROLE_RELEASEMANAGER'],
                [6,  'ROLE_WEBSITEADMIN',   'Website-Administrator',                     'ROLE_WEBSITEADMIN'],
                [7,  'ROLE_MODERATOR',      'Moderator',                                 'ROLE_MODERATOR'],
                [8,  'ROLE_CUSTOMERADMIN',  'Kundenadministrator',                       'ROLE_CUSTOMERADMIN'],
                [9,  'ROLE_CUSTOMER',       'Kunde',                                     'ROLE_CUSTOMER'],
                [10, 'ROLE_DEVELOPER',      'Entwickler',                                'ROLE_DEVELOPER'],
                [20, 'ROLE_USER',           'Standard Benutzer',                         'ROLE_USER'],
            ];

            foreach ($roleDefs as [$hier, $rName, $desc, $tag]) {
                $r = new UserRoles();
                $r->setRoleName($rName);
                $r->setRoleDescription($desc);
                $r->setRoleTag($tag);
                $r->setHierarchy($hier);
                $r->setRoleCreate(new \DateTime());
                $r->setRoleCreateBy('setup');
                $em->persist($r);
            }

            // -------------------------
            // 2. Admin-User anlegen
            // -------------------------
            $u = new User();
            $u->setEmail($email);
            $u->setUsername($username);
            $u->setPrename($prename);
            $u->setName($name);
            $u->setCustomerId("SYSTEM");
            $u->setRegDate(new \DateTime());
            $u->setLastlogindate(new \DateTime());
            $u->setIsVerified(true);
            $u->setIsActive(true);

            $u->setRoles(['ROLE_SUPREME_ADMIN']);

            // Passwort & PIN hashen
            $u->setPassword(
                $hasher->hashPassword($u, $password)
            );

            $u->setUserpin(
                $hasher->hashPassword($u, $pin)
            );

            $em->persist($u);
            $em->flush();

            // -------------------------
            // 3. Setup sperren
            // -------------------------
            file_put_contents($lockFile, "Setup finished at " . date("Y-m-d H:i:s"));

            $this->addFlash('success', 'Setup erfolgreich abgeschlossen! Der Admin wurde angelegt.');

            return $this->redirectToRoute('app_setup');
        }

        return $this->render('setup/index.html.twig');
    }
}
