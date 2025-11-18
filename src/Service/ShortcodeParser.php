<?php

namespace App\Service;

use App\Service\ShortcodeImageService;
use App\Service\ShortcodeButtonService;

use Twig\Environment;

class ShortcodeParser
{
    public function __construct(
        private readonly ShortcodeImageService $shortcodeImageService,
        private readonly ShortcodeButtonService $shortcodeButtonService, // <-- HINZUGEFÜGT
        private readonly Environment $twig
    ) {}

    public function parse(string $content): string
    {
        return preg_replace_callback('/\[\[(\w+)(.*?)\]\]/', function ($matches) {
            $shortcode = $matches[1];
            $rawAttributes = trim($matches[2]);
            $params = $this->parseAttributes($rawAttributes);

            // DEBUG TEST
            if ($shortcode === 'debug_test') {
                return "<div style='border:1px solid green;padding:10px;'>✅ ShortcodeParser läuft! Params: " . json_encode($params) . "</div>";
            }

            if ($shortcode === 'shortcode_image') {
                return $this->shortcodeImageService->renderShortcode($params['tag'] ?? '');
            }
            if ($shortcode === 'shortcode_button') {
                return $this->shortcodeButtonService->renderShortcode($params['tag'] ?? '', $params);
            }

            return "<!-- ❓ Unknown shortcode '$shortcode' -->";
        }, $content);
    }

    private function parseAttributes(string $attrString): array
{
    $result = [];

    // Erkenne key="value"
    preg_match_all('/(\w+)=(".*?"|\'.*?\'|\S+)/', $attrString, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $key = $match[1];
        $value = trim($match[2], '"\''); // Quotes entfernen falls vorhanden

        // Wandle true/false in bool um
        if (strtolower($value) === 'true') {
            $value = true;
        } elseif (strtolower($value) === 'false') {
            $value = false;
        }

        $result[$key] = $value;
    }

    return $result;
}

    
}
