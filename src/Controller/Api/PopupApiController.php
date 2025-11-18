<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PopupApiController extends AbstractController{
    #[Route('/api/popup', name: 'app_api_popup_api')]
    public function index(): Response
    {
        return $this->render('api/popup_api/index.html.twig', [
            'controller_name' => 'Api/PopupApiController',
        ]);
    }
}
