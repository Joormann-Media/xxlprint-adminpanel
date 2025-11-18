<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QRCodeGeneratorController extends AbstractController
{
    #[Route('/qr-code/generator', name: 'app_q_r_code_generator')]
    public function index(): Response
    {
        return $this->render('qr_code_generator/index.html.twig', [
            'controller_name' => 'QRCodeGeneratorController',
            'page_title' => 'QR Code Generator',
        ]);
    }
}
