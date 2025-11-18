<?php

namespace App\Service;

use App\Repository\ShortcodeButtonRepository;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class ShortcodeButtonService
{
    public function __construct(
        private readonly ShortcodeButtonRepository $repository,
        private readonly Environment $twig,
        private readonly RouterInterface $router
    ) {}

    public function renderShortcode(string $tag, array $args = []): string
    {
        $entry = $this->repository->findOneBy(['tag' => $tag, 'isActive' => true]);
        dump('Shortcode aufgerufen mit Tag: ' . $tag); // <-- sichtbar im Symfony Profiler

        if (!$entry) {
            return "<!-- ❌ ShortcodeButton '$tag' not found or inactive -->";
        }

        // Parameter-Mapping vorbereiten
        $paramList = $entry->getParamList() ? explode(',', $entry->getParamList()) : [];
        $routeParams = [];
        foreach ($paramList as $param) {
            $param = trim($param);
            if (isset($args[$param])) {
                $routeParams[$param] = $args[$param];
            }
        }

        try {
            $url = $this->router->generate($entry->getRoute(), $routeParams);
        } catch (\Throwable $e) {
            return "<!-- ⚠️ Route error for tag '$tag': " . $e->getMessage() . " -->";
        }

        return $this->twig->render('shortcodes/button.html.twig', [
            'url' => $url,
            'icon' => $entry->getIconPath(),
            'label' => $entry->getLabel(),
            'style' => $entry->getStyle() ?? 'primary',
            'tag' => $tag,
            'useModal' => $args['modal'] ?? false,
            'modalTitle' => $entry->getLabel(),
        ]);
    }
}
