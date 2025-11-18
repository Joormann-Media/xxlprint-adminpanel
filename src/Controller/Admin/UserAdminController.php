<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\UserRolesRepository;
use App\Service\UserCleanupService;
use App\Form\Admin\UserAdminFormType;
use App\Service\LdapUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class UserAdminController extends AbstractController
{
    #[Route('/admin/user-admin', name: 'app_admin_user_admin_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $filterLdap = $request->query->get('ldap');
        $users = $userRepository->createQueryBuilder('u');
        if ($filterLdap === '1') {
            $users->andWhere('u.ldapSyncedAt IS NOT NULL');
        } elseif ($filterLdap === '0') {
            $users->andWhere('u.ldapSyncedAt IS NULL');
        }
        $allUsers = $users->getQuery()->getResult();

        return $this->render('admin/user_admin/index.html.twig', [
            'all_users' => $userRepository->findAll(),
            'dummy_users' => $userRepository->findBy(['isVerified' => false]),
            'page_title' => 'Benutzerverwaltung',
        ]);
    }

    #[Route('/admin/user-admin/cleanup/{id<\d+>}', name: 'app_admin_user_admin_cleanup', methods: ['POST'])]
    public function cleanup(int $id, UserRepository $userRepository, UserCleanupService $cleanupService): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            $this->addFlash('danger', '❌ Benutzer nicht gefunden.');
            return $this->redirectToRoute('app_admin_user_admin_index');
        }

        $result = $cleanupService->deleteUserAndRelatedData($user);

        $this->addFlash($result['success'] ? 'success' : 'danger', $result['success']
            ? "✅ Benutzer wurde erfolgreich gelöscht."
            : "❌ Fehler beim Löschen: {$result['message']}"
        );

        foreach ($result['log'] as $line) {
            $this->addFlash('info', $line);
        }

        return $this->redirectToRoute('app_admin_user_admin_index');
    }

    #[Route('/admin/user-admin/delete-dummy', name: 'app_admin_user_admin_delete_dummy', methods: ['GET'])]
    public function deleteDummyUsers(UserRepository $userRepo, UserCleanupService $cleanupService): Response
    {
        $dummyUsers = $userRepo->findBy(['isVerified' => false]);
        $count = 0;
        $errors = 0;

        foreach ($dummyUsers as $dummy) {
            $result = $cleanupService->deleteUserAndRelatedData($dummy);
            $result['success'] ? $count++ : $errors++;
        }

        $this->addFlash('success', "✅ $count Dummy-User gelöscht.");
        if ($errors > 0) {
            $this->addFlash('danger', "⚠️ $errors Dummy-User konnten nicht gelöscht werden.");
        }

        return $this->redirectToRoute('app_admin_user_admin_index');
    }

    #[Route('/admin/user-admin/edit/{id?}', name: 'app_admin_user_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserRolesRepository $userRolesRepository,
        ?int $id = null
    ): Response {
        $user = $id ? $userRepository->find($id) : new User();

        if (!$user) {
            $this->addFlash('danger', '❌ Benutzer nicht gefunden.');
            return $this->redirectToRoute('app_admin_user_admin_index');
        }

        $form = $this->createForm(UserAdminFormType::class, $user, [
            'roles_repository' => $userRolesRepository,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $id ? '✅ Benutzer erfolgreich aktualisiert.' : '✅ Benutzer erfolgreich erstellt.');
            return $this->redirectToRoute('app_admin_user_admin_index');
        }

        return $this->render('admin/user_admin/edit.html.twig', [
            'form' => $form->createView(),
            'page_title' => $id ? 'Benutzer bearbeiten' : 'Benutzer erstellen',
        ]);
    }

    #[Route('/admin/user-admin/new', name: 'app_admin_user_admin_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRolesRepository $userRolesRepository
    ): Response {
        $user = new User();
        $form = $this->createForm(UserAdminFormType::class, $user, [
            'roles_repository' => $userRolesRepository,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', '✅ Neuer Benutzer erfolgreich erstellt.');
            return $this->redirectToRoute('app_admin_user_admin_index');
        }

        return $this->render('admin/user_admin/edit.html.twig', [
            'form' => $form->createView(),
            'page_title' => 'Neuen Benutzer erstellen',
        ]);
    }

    #[Route('/admin/user-admin/ldap-sync/{id<\d+>}', name: 'admin_user_ldap_sync')]
    public function syncToLdap(int $id, UserRepository $userRepo, LdapUserService $ldapService, EntityManagerInterface $em): Response
    {
        $user = $userRepo->find($id);

        if (!$user) {
            $this->addFlash('danger', '❌ Benutzer nicht gefunden.');
            return $this->redirectToRoute('app_admin_user_admin_index');
        }

        $ldapService->syncUser($user);
        $user->setLdapSyncedAt(new \DateTime());

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', "✅ Benutzer {$user->getUsername()} wurde erfolgreich in LDAP synchronisiert.");
        return $this->redirectToRoute('app_admin_user_admin_index');
    }

    #[Route('/admin/user-admin/ldap-update/{id<\d+>}', name: 'admin_user_ldap_update')]
    public function updateLdapEntry(int $id, UserRepository $userRepo, LdapUserService $ldapService): Response
    {
        $user = $userRepo->find($id);

        if (!$user) {
            $this->addFlash('danger', '❌ Benutzer nicht gefunden.');
        } else {
            $ldapService->updateUser($user);
            $this->addFlash('success', "🔄 LDAP-Eintrag für Benutzer {$user->getUsername()} aktualisiert.");
        }

        return $this->redirectToRoute('app_admin_user_admin_index');
    }

    #[Route('/admin/user-admin/ldap-sync-all', name: 'admin_user_ldap_sync_all')]
    public function syncAllUnsyncedUsers(UserRepository $userRepo, LdapUserService $ldapService, EntityManagerInterface $em): Response
    {
        $users = $userRepo->findBy(['ldapSyncedAt' => null]);
        $count = 0;

        foreach ($users as $user) {
            try {
                $ldapService->syncUser($user);
                $user->setLdapSyncedAt(new \DateTime());
                $em->persist($user);
                $count++;
            } catch (\Exception $e) {
                $this->addFlash('danger', "❌ Fehler bei Benutzer {$user->getUsername()}: " . $e->getMessage());
            }
        }

        $em->flush();
        $this->addFlash('success', "✅ $count Benutzer in LDAP synchronisiert.");

        return $this->redirectToRoute('app_admin_user_admin_index');
    }

    public function configureActions(Actions $actions): Actions
    {
        $syncToLdap = Action::new('syncToLdap', '🔄 In LDAP')
            ->linkToRoute('admin_user_ldap_sync', fn(User $user) => ['id' => $user->getId()])
            ->setCssClass('btn btn-outline-success')
            ->displayIf(fn(User $user) => $user->getLdapSyncedAt() === null);

        $updateLdap = Action::new('updateLdap', '🛠 LDAP aktualisieren')
            ->linkToRoute('admin_user_ldap_update', fn(User $user) => ['id' => $user->getId()])
            ->setCssClass('btn btn-warning')
            ->displayIf(fn(User $user) => $user->getLdapSyncedAt() !== null);

        return $actions
            ->add(Crud::PAGE_INDEX, $syncToLdap)
            ->add(Crud::PAGE_INDEX, $updateLdap)
            ->add(Crud::PAGE_DETAIL, $syncToLdap)
            ->add(Crud::PAGE_DETAIL, $updateLdap);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('username'),
            TextField::new('email'),
            DateTimeField::new('ldapSyncedAt', 'LDAP-Sync')
                ->setFormat('yyyy-MM-dd HH:mm')
                ->hideOnForm(),
        ];
    }
}
