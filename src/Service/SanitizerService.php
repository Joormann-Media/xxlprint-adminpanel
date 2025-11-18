<?php

namespace App\Service;

class SanitizerService
{
    public function clean(?string $input): ?string
    {
        if (null === $input) return null;

        $replacements = [
            'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue',
            'Ä' => 'ae', 'Ö' => 'oe', 'Ü' => 'ue',
            'ß' => 'ss'
        ];
        $input = strtr($input, $replacements);
        $input = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $input);
        $input = strtolower($input);
        return preg_replace('/[^a-z0-9]/', '', $input);
    }

    // 👇 Alias
    public function sanitize(?string $input): ?string
    {
        return $this->clean($input);
    }
}

