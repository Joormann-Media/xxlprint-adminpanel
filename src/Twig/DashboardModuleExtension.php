<?php

namespace App\Twig;

use App\Entity\UserDashboardConfig;
use App\Service\DashboardModuleRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DashboardModuleExtension extends AbstractExtension
{
    public function __construct(
        private DashboardModuleRenderer $renderer,
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_dashboard_module', [$this, 'renderModule'], ['is_safe' => ['html']]),
        ];
    }

    public function renderModule(UserDashboardConfig $config): string
    {
        return $this->renderer->render($config);
    }
}

