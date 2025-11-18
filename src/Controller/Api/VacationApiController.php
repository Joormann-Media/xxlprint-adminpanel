<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VacationApiController extends AbstractController{

    #[Route('/api/vacation', name: 'app_api_vacation_api')]
    public function index(): Response
    {
        return $this->render('api/vacation_api/index.html.twig', [
            'controller_name' => 'Api/VacationApiController',
        ]);
    }
}
