<?php

namespace App\Controller;

use App\Entity\ShortcodeImage;
use App\Form\ShortcodeImageType;
use App\Repository\ShortcodeImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ShortcodeImageService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


#[Route('/admin/shortcode')]
final class ShortcodeImageController extends AbstractController
{
    private ShortcodeImageService $shortcodeImageService;

    public function __construct(ShortcodeImageService $shortcodeImageService)
    {
        $this->shortcodeImageService = $shortcodeImageService;
    }

    #[Route(name: 'app_shortcode_image_index', methods: ['GET'])]
    public function index(ShortcodeImageRepository $shortcodeImageRepository): Response
    {
        return $this->render('shortcode_image/index.html.twig', [
            'shortcode_images' => $shortcodeImageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_shortcode_image_new', methods: ['GET', 'POST'])]
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    \App\Service\ShortcodeParser $shortcodeParser,
    #[Autowire('%kernel.project_dir%')] string $projectDir
): Response {
    $shortcodeImage = new ShortcodeImage();
    $form = $this->createForm(ShortcodeImageType::class, $shortcodeImage);
    $form->handleRequest($request);

    $previewHtml = '';
    if ($shortcodeImage->getTag()) {
        $previewHtml = $shortcodeParser->parse('[[shortcode_image tag="' . $shortcodeImage->getTag() . '"]]');
    }

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($shortcodeImage);
        $entityManager->flush();

        return $this->redirectToRoute('app_shortcode_image_index');
    }

    return $this->render('shortcode_image/new.html.twig', [
        'shortcode_image' => $shortcodeImage,
        'form' => $form,
        'previewHtml' => $previewHtml,
        'icons' => $this->getIconList($projectDir),
        'page_title' => 'Shortcode-Image erstellen',
    ]);


}


    #[Route('/{id<\d+>}', name: 'app_shortcode_image_show', methods: ['GET'])]
    public function show(ShortcodeImage $shortcodeImage): Response
    {
        return $this->render('shortcode_image/show.html.twig', [
            'shortcode_image' => $shortcodeImage,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_shortcode_image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ShortcodeImage $shortcodeImage, EntityManagerInterface $entityManager, \App\Service\ShortcodeParser $shortcodeParser,
    #[Autowire('%kernel.project_dir%')] string $projectDir): Response
    {
        $form = $this->createForm(ShortcodeImageType::class, $shortcodeImage);
        $form->handleRequest($request);

        // Generate previewHtml immediately if the tag exists
        $previewHtml = '';
        if ($shortcodeImage->getTag()) {
            $previewHtml = $shortcodeParser->parse('[[shortcode_image code="' . $shortcodeImage->getTag() . '"]]');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_shortcode_image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shortcode_image/edit.html.twig', [
            'shortcode_image' => $shortcodeImage,
            'form' => $form,
            'previewHtml' => $previewHtml,
            'icons' => $this->getIconList($projectDir),
            'page_title' => 'Shortcode-Image bearbeiten',
            'page_description' => 'Hier bearbeiten Sie ein bestehendes Bild.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_shortcode_image_delete', methods: ['POST'])]
    public function delete(Request $request, ShortcodeImage $shortcodeImage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shortcodeImage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($shortcodeImage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_shortcode_image_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/shortcode/preview', name: 'app_shortcode_image_preview', methods: ['POST'])]
public function preview(Request $request, ShortcodeImageService $shortcodeImageService): Response
{
    $tag = $request->request->get('tag');

    // Wenn zusätzlich Formdaten kommen (für Live-Vorschau)
    $data = [
        'filename' => $request->request->get('filename'),
        'path' => $request->request->get('path'),
        'title' => $request->request->get('title'),
        'description' => $request->request->get('description'),
    ];

    $html = $shortcodeImageService->renderShortcode($tag, $data);
    return new Response($html);
}

private function getIconList(string $projectDir): array
{
    $iconDir = $projectDir . '/public/gfx/shortcode/';
    if (!is_dir($iconDir)) {
        return [];
    }

    $files = array_filter(scandir($iconDir), function ($f) {
        return !in_array($f, ['.', '..']) && preg_match('/\.(png|svg|jpg|jpeg)$/i', $f);
    });

    sort($files);
    return array_map(fn($f) => [
        'url' => '/gfx/shortcode/' . $f, // Ensure 'url' key is defined
        'filename' => $f
    ], $files);
}



}
