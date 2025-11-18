<?php

namespace App\Twig;

use App\Service\ShortcodeParser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ShortcodeExtension extends AbstractExtension
{
    private ShortcodeParser $parser;

    public function __construct(ShortcodeParser $parser)
    {
        $this->parser = $parser;
    }

    public function getFilters(): array
    {
        
        return [
            new TwigFilter('shortcode', [$this->parser, 'parse']),
        ];
    }
}
