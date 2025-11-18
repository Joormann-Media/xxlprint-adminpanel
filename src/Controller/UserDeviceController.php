<?php

namespace App\Controller;

use App\Entity\UserDevice;
use App\Form\UserDeviceType;
use App\Repository\UserDeviceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user-device')]
final class UserDeviceController extends AbstractController
{
    // === ADMIN ===

    #[Route(name: 'app_user_device_index', methods: ['GET'])]
    public function index(UserDeviceRepository $repo): Response
    {
        return $this->render('user_device/index.html.twig', [
            'user_devices' => $repo->findAll(),
            'page_title' => 'Benutzergeräte',
            'page_description' => 'Verwalte hier alle Geräte im System.',
        ]);
    }

    #[Route('/new', name: 'app_user_device_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $device = new UserDevice();
        $form = $this->createForm(UserDeviceType::class, $device);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($device);
            $em->flush();

            return $this->redirectToRoute('app_user_device_index');
        }

        return $this->render('user_device/new.html.twig', [
            'user_device' => $device,
            'form' => $form,
            'page_title' => 'Neues Gerät erstellen',
            'page_description' => 'Lege ein neues Gerät für einen Benutzer an.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_device_show', methods: ['GET'])]
    public function show(UserDevice $userDevice): Response
    {
        return $this->render('user_device/show.html.twig', [
            'user_device' => $userDevice,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_device_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserDevice $userDevice, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserDeviceType::class, $userDevice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_user_device_index');
        }

        return $this->render('user_device/edit.html.twig', [
            'user_device' => $userDevice,
            'form' => $form,
            'page_title' => 'Gerät bearbeiten',
            'page_description' => 'Bearbeite die Details eines registrierten Geräts.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_device_delete', methods: ['POST'])]
    public function delete(Request $request, UserDevice $userDevice, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $userDevice->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($userDevice);
            $em->flush();
        }

        return $this->redirectToRoute('app_user_device_index');
    }

    // === ME ===

    #[Route('/me', name: 'app_user_device_me', methods: ['GET'])]
    public function myDevices(UserDeviceRepository $repo): Response
    {
        $user = $this->getUser();
        $devices = $repo->findBy(['user' => $user]);

        return $this->render('user_device/my.html.twig', [
            'user_devices' => $devices,
            'page_title' => '📱 Meine Geräte',
            'page_description' => 'Verwalte hier deine registrierten Geräte.',
        ]);
    }

    #[Route('/me/add', name: 'app_user_device_me_add', methods: ['GET', 'POST'])]
    public function addForMe(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $device = new UserDevice();
        $device->setUser($user);

        $form = $this->createForm(UserDeviceType::class, $device);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($device);
            $em->flush();

            $this->addFlash('success', 'Gerät erfolgreich hinzugefügt.');
            return $this->redirectToRoute('app_user_device_me');
        }

        // Ensure the entity is persisted before rendering the form
        if (!$device->getId()) {
            $em->persist($device);
        }

        return $this->render('user_device/add_my.html.twig', [
            'form' => $form,
            'page_title' => '📲 Gerät hinzufügen',
            'page_description' => 'Registriere ein neues Gerät für dein Benutzerkonto.',
        ]);
    }

    #[Route('/me/edit/{id<\d+>}', name: 'app_user_device_me_edit', methods: ['GET', 'POST'])]
    public function editForMe(Request $request, UserDevice $device, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($device->getUser() !== $user) {
            throw $this->createAccessDeniedException('Nicht erlaubt.');
        }

        $form = $this->createForm(UserDeviceType::class, $device);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Gerät aktualisiert.');
            return $this->redirectToRoute('app_user_device_me');
        }

        return $this->render('user_device/edit_my.html.twig', [
            'form' => $form,
            'page_title' => '📲 Gerät bearbeiten',
        ]);
    }

    #[Route('/me/delete/{id<\d+>}', name: 'app_user_device_me_delete', methods: ['POST'])]
    public function deleteForMe(Request $request, UserDevice $device, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($device->getUser() !== $user) {
            throw $this->createAccessDeniedException('Nicht erlaubt.');
        }

        if ($this->isCsrfTokenValid('delete' . $device->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($device);
            $em->flush();
            $this->addFlash('success', 'Gerät gelöscht.');
        }

        return $this->redirectToRoute('app_user_device_me');
    }
}
