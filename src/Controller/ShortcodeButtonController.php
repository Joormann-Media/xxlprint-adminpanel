<?php

namespace App\Controller;

use App\Entity\ShortcodeButton;
use App\Form\ShortcodeButtonType;
use App\Repository\ShortcodeButtonRepository;
use App\Service\ShortcodeButtonService;
use App\Service\ShortcodeParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Route('/admin/shortcode-button')]
final class ShortcodeButtonController extends AbstractController
{
    public function __construct(
        private readonly ShortcodeButtonService $shortcodeButtonService
    ) {}

    #[Route(name: 'app_shortcode_button_index', methods: ['GET'])]
    public function index(ShortcodeButtonRepository $repo): Response
    {
        return $this->render('shortcode_button/index.html.twig', [
            'shortcode_buttons' => $repo->findAll(),
            'page_title' => 'Shortcode-Buttons',
            'page_description' => 'Hier können Sie Shortcode-Buttons verwalten.',
        ]);
    }

    #[Route('/new', name: 'app_shortcode_button_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        ShortcodeParser $shortcodeParser,
        #[Autowire('%kernel.project_dir%')] string $projectDir
    ): Response {
        $shortcodeButton = new ShortcodeButton();
        $form = $this->createForm(ShortcodeButtonType::class, $shortcodeButton);
        $form->handleRequest($request);

        $previewHtml = $this->renderPreview($shortcodeButton->getTag());

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($shortcodeButton);
            $em->flush();

            $this->addFlash('success', '✅ Shortcode-Button erfolgreich erstellt.');
            return $this->redirectToRoute('app_shortcode_button_index');
        }

        return $this->render('shortcode_button/new.html.twig', [
            'shortcode_button' => $shortcodeButton,
            'form' => $form,
            'previewHtml' => $previewHtml,
            'icons' => $this->getIconList($projectDir),
            'page_title' => 'Neuer Shortcode-Button',
            'page_description' => 'Hier können Sie einen neuen Shortcode-Button erstellen.',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_shortcode_button_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ShortcodeButton $shortcodeButton,
        EntityManagerInterface $em,
        ShortcodeParser $shortcodeParser,
        #[Autowire('%kernel.project_dir%')] string $projectDir
    ): Response {
        $form = $this->createForm(ShortcodeButtonType::class, $shortcodeButton);
        $form->handleRequest($request);

        $previewHtml = $this->renderPreview($shortcodeButton->getTag());

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', '✅ Änderungen gespeichert!');
            return $this->redirectToRoute('app_shortcode_button_index');
        }

        return $this->render('shortcode_button/edit.html.twig', [
            'shortcode_button' => $shortcodeButton,
            'form' => $form,
            'previewHtml' => $previewHtml,
            'icons' => $this->getIconList($projectDir),
            'page_title' => 'Shortcode-Button bearbeiten',
            'page_description' => 'Hier bearbeiten Sie einen bestehenden Button.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_shortcode_button_show', methods: ['GET'])]
    public function show(ShortcodeButton $shortcodeButton): Response
    {
        return $this->render('shortcode_button/show.html.twig', [
            'shortcode_button' => $shortcodeButton,
        ]);
    }

    #[Route('/{id<\d+>}/delete', name: 'app_shortcode_button_delete', methods: ['POST'])]
    public function delete(Request $request, ShortcodeButton $shortcodeButton, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $shortcodeButton->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($shortcodeButton);
            $em->flush();
            $this->addFlash('success', '🗑️ Shortcode-Button gelöscht.');
        }

        return $this->redirectToRoute('app_shortcode_button_index');
    }

    #[Route('/preview', name: 'app_shortcode_button_preview', methods: ['POST'])]
    public function preview(Request $request): Response
    {
        $tag = $request->request->get('tag');
        $args = [];

        foreach ($request->request->all() as $key => $val) {
            if ($key !== 'tag') {
                $args[$key] = $val;
            }
        }

        $html = $this->shortcodeButtonService->renderShortcode($tag, $args);
        return new Response($html);
    }

    #[Route('/upload-icon', name: 'app_shortcode_button_upload_icon', methods: ['POST'])]
    public function uploadIcon(
        Request $request,
        #[Autowire('%kernel.project_dir%')] string $projectDir
    ): JsonResponse {
        $icon = $request->files->get('icon');
        if (!$icon || !$icon->isValid()) {
            return new JsonResponse([
                'success' => false,
                'message' => '❌ Kein oder ungültiges Icon übergeben.',
                'path' => 'gfx/shortcode/' . $filename, // OHNE 'public/', OHNE Slash vorn
            ]);
        }

        $uploadFolder = 'gfx/shortcode';
        $targetDir = $projectDir . '/public/' . $uploadFolder;
        $filename = uniqid('icon_') . '.' . $icon->guessExtension();

        try {
            $icon->move($targetDir, $filename);

            return new JsonResponse([
                'success' => true,
                'filename' => $filename,
                'path' => '/' . $uploadFolder . '/' . $filename, // für Browser korrekt
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => '❌ Upload-Fehler: ' . $e->getMessage(),
            ]);
        }
    }
    #[Route('/admin/shortcode/test', name: 'app_shortcode_test', methods: ['GET', 'POST'])]
public function test(Request $request, ShortcodeParser $shortcodeParser): Response
{
    $shortcode = $request->get('shortcode', '[[shortcode_button tag="Test"]]'); // Default Test

    $rendered = null;
    $error = null;

    try {
        $rendered = $shortcodeParser->parse($shortcode);
    } catch (\Throwable $e) {
        $error = $e->getMessage();
    }

    return $this->render('shortcodes/test.html.twig', [
        'shortcode' => $shortcode,
        'rendered' => $rendered,
        'error' => $error,
        'page_title' => '🔬 Shortcode-Testseite',
        'page_description' => 'Hier kannst du jeden beliebigen Shortcode live testen.',
    ]);
}


    private function renderPreview(?string $tag): string
    {
        return $tag ? $this->shortcodeButtonService->renderShortcode($tag, ['id' => 999]) : '';
    }

    private function getIconList(string $projectDir): array
    {
        $iconDir = $projectDir . '/public/gfx/shortcode/';
        if (!is_dir($iconDir)) {
            return [];
        }

        $files = array_filter(scandir($iconDir), fn($f) =>
            !in_array($f, ['.', '..']) && preg_match('/\\.(png|jpg|jpeg|svg)$/i', $f)
        );

        sort($files);

        return array_map(fn($f) => '/gfx/shortcode/' . $f, $files);
    }
}
