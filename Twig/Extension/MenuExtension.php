<?php

namespace App\Twig\Extension;

use App\Service\MenuService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    private MenuService $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getStructuredMenu', [$this->menuService, 'getStructuredMenu']),
        ];
    }
}
