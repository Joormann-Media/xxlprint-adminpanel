<?php

namespace App\Controller;

use App\Entity\PopUpManager;
use App\Entity\WebsiteSettings;
use App\Form\PopUpManagerType;
use App\Repository\PopUpManagerRepository;
use App\Repository\ShortcodeButtonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/admin/popup-manager')]
final class PopUpManagerController extends AbstractController
{
    #[Route(name: 'app_pop_up_manager_index', methods: ['GET'])]
    public function index(PopUpManagerRepository $popUpManagerRepository,ShortcodeButtonRepository $buttonRepo): Response
    {
        return $this->render('pop_up_manager/index.html.twig', [
            'pop_up_managers' => $popUpManagerRepository->findAll(),
            'page_title' => 'Pop-Up-Manager - Übersicht',
            'help_button' => $buttonRepo->findOneBy(['tag' => 'help']),
            'shortcode_buttons' => $buttonRepo->findAll(),

        ]);
    }

    #[Route('/new', name: 'app_pop_up_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, HttpClientInterface $httpClient): Response
    {
        $popUpManager = new PopUpManager();
        $form = $this->createForm(PopUpManagerType::class, $popUpManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        // Speichern des neuen PopUpManagers in der Datenbank
            $entityManager->persist($popUpManager);
            $entityManager->flush();

        // Senden der API-Anfrage, um das PopUpManager-Objekt zu aktualisieren
        $response = $httpClient->request('PUT', 'https://admin.joormann-media.de/api/website-settings/update', [
            'json' => [
                'websiteMessageId' => $popUpManager->getId(),
                'websiteMode' => 'popup', // Beispielwert, je nachdem, welche Werte du setzen möchtest
                'lastUpdate' => (new \DateTime())->format('Y-m-d H:i:s'), // Aktuelles Datum und Uhrzeit
                'lastUpdateBy' => $this->getUser()->getUsername(), // Benutzername des aktuellen Benutzers
                'activeUntil' => $popUpManager->getPopupExpires()->format('Y-m-d H:i:s'), // Ablaufdatum des Popups
                //'id' => '3', // ID des WebsiteSettings-Objekts, das aktualisiert werden soll
                // Weitere Daten hinzufügen, die die API benötigt
            ],
            'headers' => [
                'Content-Type' => 'application/json',
                //'Authorization' => 'Bearer ' . $this->getUser()->getApiToken(), // Wenn du ein Token benötigst
            ],
        ]);

        // Prüfen, ob die API-Anfrage erfolgreich war
        if ($response->getStatusCode() === 200) {
            // Wenn die Anfrage erfolgreich war, umleiten
            return $this->redirectToRoute('app_pop_up_manager_index', [], Response::HTTP_SEE_OTHER);
        } else {
            // Fehlerbehandlung, wenn die API nicht erfolgreich war
            $this->addFlash('error', 'Fehler beim Senden der Daten an die API.');
        }
        }

        return $this->render('pop_up_manager/new.html.twig', [
            'pop_up_manager' => $popUpManager,
            'form' => $form,
            'page_title' => 'Pop-Up-Manager - Neuer Eintrag',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_pop_up_manager_show', methods: ['GET'])]
    public function show(PopUpManager $popUpManager): Response
    {
        return $this->render('pop_up_manager/show.html.twig', [
            'pop_up_manager' => $popUpManager,
            'page_title' => 'Pop-Up-Manager - Details',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_pop_up_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PopUpManager $popUpManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PopUpManagerType::class, $popUpManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pop_up_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pop_up_manager/edit.html.twig', [
            'pop_up_manager' => $popUpManager,
            'form' => $form,
            'page_title' => 'Pop-Up-Manager - Bearbeiten',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_pop_up_manager_delete', methods: ['POST'])]
    public function delete(Request $request, PopUpManager $popUpManager, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$popUpManager->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($popUpManager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pop_up_manager_index', [], Response::HTTP_SEE_OTHER);
    }
}
