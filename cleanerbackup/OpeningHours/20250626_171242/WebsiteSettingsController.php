<?php

namespace App\Controller;

use App\Entity\WebsiteSettings;
use App\Form\WebsiteSettingsType;
use App\Repository\WebsiteSettingsRepository;
use App\Repository\PopUpManagerRepository; // PopUpManagerRepository hinzufügen
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/website-settings')]
final class WebsiteSettingsController extends AbstractController
{
    #[Route(name: 'app_website_settings_index', methods: ['GET'])]
    public function index(WebsiteSettingsRepository $websiteSettingsRepository): Response
    {
        // Verwenden des EntityManagers, um WebsiteSettings zu laden
        $websiteSettings = $websiteSettingsRepository->findAll();

        return $this->render('website_settings/index.html.twig', [
            'website_settings' => $websiteSettings,
            'page_title' => 'Website Einstellungen',
        ]);
    }

    #[Route('/new', name: 'app_website_settings_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PopUpManagerRepository $popUpManagerRepository): Response
    {
        $websiteSetting = new WebsiteSettings();

        // Hole die PopUpManager-Objekte für das Dropdown
        $popupChoices = $popUpManagerRepository->findBy(['popupCategory' => 1]);

        // Erstelle das Formular und übergebe die PopUpManager-Objekte
        $form = $this->createForm(WebsiteSettingsType::class, $websiteSetting);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($websiteSetting);
            $entityManager->flush();

            return $this->redirectToRoute('app_website_settings_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('website_settings/new.html.twig', [
            'website_setting' => $websiteSetting,
            'form' => $form,
            'page_title' => 'Website Einstellungen erstellen',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_website_settings_show', methods: ['GET'])]
    public function show(WebsiteSettings $websiteSetting): Response
    {
        return $this->render('website_settings/show.html.twig', [
            'website_setting' => $websiteSetting,
            'page_title' => 'Website Einstellungen anzeigen',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_website_settings_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WebsiteSettings $websiteSetting, EntityManagerInterface $entityManager, PopUpManagerRepository $popUpManagerRepository): Response
    {
        // Hole die PopUpManager-Objekte für das Dropdown
        $popupChoices = $popUpManagerRepository->findBy(['popupCategory' => 1]);

        $form = $this->createForm(WebsiteSettingsType::class, $websiteSetting);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_website_settings_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('website_settings/edit.html.twig', [
            'website_setting' => $websiteSetting,
            'form' => $form,
            'page_title' => 'Website Einstellungen bearbeiten',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_website_settings_delete', methods: ['POST'])]
    public function delete(Request $request, WebsiteSettings $websiteSetting, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $websiteSetting->getId(), $request->request->get('_token'))) {
            $entityManager->remove($websiteSetting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_website_settings_index', [], Response::HTTP_SEE_OTHER);
    }
}
