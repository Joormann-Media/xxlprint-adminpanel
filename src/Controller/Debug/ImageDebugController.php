<?php

namespace App\Controller\Debug;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageDebugController extends AbstractController
{
    #[Route('/debug/image', name: 'debug_image')]
    public function imageDebug(): Response
    {
        return $this->render('debug/image_debug.html.twig');
    }
}
