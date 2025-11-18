<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home_redirect')]
    public function homeRedirect(): RedirectResponse
    {
        return $this->redirectToRoute('app_home'); // Leitet auf /home weiter
    }

    #[Route('/home', name: 'app_home')]
    public function index(): RedirectResponse
    {
        return $this->redirect('/admin'); // Leitet auf das Admin-Dashboard weiter
    }
}
