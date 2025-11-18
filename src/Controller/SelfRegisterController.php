<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SelfRegisterController extends AbstractController
{
    #[Route('/self/register', name: 'app_self_register')]
    public function index(): Response
    {
        return $this->render('self_register/index.html.twig', [
            'controller_name' => 'SelfRegisterController',
        ]);
    }
}
