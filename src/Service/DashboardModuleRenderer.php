<?php

namespace App\Service;

use App\Entity\UserDashboardConfig;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment;

class DashboardModuleRenderer
{
    public function __construct(
        private Environment $twig,
        private Security $security
    ) {}
    
    public function renderPreview(string $rawTwig): string
{
    if (trim($rawTwig) === '') {
        return '<em class="text-muted">Kein Inhalt gespeichert.</em>';
    }

    try {
        $template = $this->twig->createTemplate($rawTwig);

        // ✅ eingeloggten User holen
        $user = $this->security->getUser();

        return $template->render([
            'app' => ['user' => $user],
            'modulePreview' => true,
        ]);
    } catch (\Throwable $e) {
        return sprintf('<div class="text-danger">Renderfehler: %s</div>', $e->getMessage());
    }
}

    public function render(UserDashboardConfig $config): string
    {
    $module = $config->getModule();
    $user = $config->getUser();

    // 1. Content aus Modul oder Settings holen
    $rawTwig = $module->getContent() ?? ($config->getSettings()['content'] ?? null);

    if (!$rawTwig) {
        return '<em class="text-muted">Kein Inhalt gespeichert.</em>';
    }

    try {
        // 2. Twig "on the fly" rendern
        $template = $this->twig->createTemplate($rawTwig);
        return $template->render([
            'config' => $config,
            'user' => $user,
            'settings' => $config->getSettings(),
            'app' => ['user' => $user], // optional: Zugriff auf {{ app.user }}
        ]);
    } catch (\Throwable $e) {
        return sprintf('<div class="text-danger">Renderfehler: %s</div>', $e->getMessage());
    }
    
}

}

