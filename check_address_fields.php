#!/usr/bin/env php
<?php

$entityDir = __DIR__ . '/src/Entity';
$addressKeywords = [
    'street', 'strasse', 'straße',
    'address', 'adresse',
    'plz', 'postalcode', 'zip',
    'city', 'stadt',
    'district', 'bezirk', 'ortsteil',
    'state', 'bundesland',
    'country', 'land',
    'latitude', 'longitude'
];

echo "🔍 Suche Entities mit Adressfeldern...\n\n";

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($entityDir));
$entitiesFound = 0;

foreach ($rii as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $content = file_get_contents($file->getPathname());
    preg_match_all('/private\s+\??[A-Za-z0-9_\\\\]+\s+\$([A-Za-z0-9_]+)/', $content, $matches);

    $fields = $matches[1] ?? [];
    $addressFields = array_filter($fields, function ($f) use ($addressKeywords) {
        foreach ($addressKeywords as $keyword) {
            if (stripos($f, $keyword) !== false) return true;
        }
        return false;
    });

    if (!empty($addressFields)) {
        $entitiesFound++;
        echo "📦 " . $file->getFilename() . ":\n";
        foreach ($addressFields as $f) {
            echo "   - $f\n";
        }
        echo "\n";
    }
}

if ($entitiesFound === 0) {
    echo "❌ Keine Adressfelder in Entities gefunden.\n";
} else {
    echo "✅ $entitiesFound Entities mit Adressfeldern gefunden.\n";
}
