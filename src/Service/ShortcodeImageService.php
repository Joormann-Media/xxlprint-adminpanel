<?php

namespace App\Service;

use App\Repository\ShortcodeImageRepository;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;

class ShortcodeImageService
{
    public function __construct(
        private readonly ShortcodeImageRepository $repository,
        private readonly Environment $twig,
        #[Autowire('%kernel.project_dir%/public/')] private string $publicPath,
    ) {}

    public function renderShortcode(string $tag, array $formData = []): string
{
    // 1️⃣ Wenn kein Datenbankeintrag vorhanden – nutze Fallback aus Formdaten
    $entry = $this->repository->findOneBy(['tag' => $tag, 'isActive' => true]);
    $compact = isset($formData['compact']) && $formData['compact'] === 'true';


    if (!$entry && !empty($formData['filename'])) {
        $imagePath = $formData['path']
            ? trim($formData['path'], '/') . '/' . $formData['filename']
            : $formData['filename'];

            return $this->twig->render('shortcodes/image.html.twig', [
                'src' => '/' . $imagePath,
                'title' => $entry->getTitle() ?: $tag,
                'description' => $entry->getDescription(),
                'key' => $tag,
                'compact' => $formData['compact'] ?? false,
            ]);
    }

    if (!$entry) {
        return "<div class='text-danger'>❌ Kein Eintrag für <strong>$tag</strong> gefunden</div>";
    }
    
    $imagePath = $entry->getPath()
        ? trim($entry->getPath(), '/') . '/' . $entry->getFilename()
        : $entry->getFilename();
    
    return $this->twig->render('shortcodes/image.html.twig', [ // ✅ richtiger Template-Pfad
        'src' => '/' . $imagePath,
        'title' => $entry->getTitle() ?: $tag,
        'description' => $entry->getDescription(),
        'key' => $tag,
        'compact' => $formData['compact'] ?? false,
    ]);
    
        
    
}

}
