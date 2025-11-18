<?php

// src/Service/SignatureGenerator.php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;
use App\Entity\EmailSignature;

class SignatureGenerator
{
    public function __construct(
        private Environment $twig,
        private KernelInterface $kernel,
        
    ) {}

    public function renderSignature(EmailSignature $sig): string
    {
                // Falls htmlOutput gesetzt ist, wird es als Signatur verwendet
        if ($signature->getHtmlOutput()) {
            return $signature->getHtmlOutput();
        }

        return $this->twig->render('signature/classic.html.twig', [
            'name' => $sig->getName(),
            'position' => $sig->getPosition(),
            'company' => $sig->getCompany(),
            'email' => $sig->getEmail(),
            'phone' => $sig->getPhone(),
            'mobile' => $sig->getMobile(),
            'website' => $sig->getWebsite(),
            'logo' => $sig->getLogoPath(), // muss ggf. URL sein
            'disclaimer' => $sig->getDisclaimer(),
            //'color' => '#000', // später dynamisch
        ]);
    }

    public function generate(EmailSignature $signature): string
    {
        $template = 'signature/' . $signature->getTemplate() . '.html.twig';
        // Falls htmlOutput gesetzt ist, wird es als Signatur verwendet
    if ($signature->getHtmlOutput()) {
        return $signature->getHtmlOutput();
    }
        // Dynamische Parameter für die Signatur
        $params = [
            'signature' => $signature,
            //'color' => $signature->getColor() ?: '#000',  // Falls eine Farbe dynamisch gesetzt wird
            'logo' => $signature->getLogoPath(),  // Logo als Pfad oder URL
        ];

        // Zusätzliche benutzerdefinierte Parameter für Templates
        //if ($signature->getCustomParams()) {
        //    $params = array_merge($params, $signature->getCustomParams());
        //}

        return $this->twig->render($template, $params);
    }
}

