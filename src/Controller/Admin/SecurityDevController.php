<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityDevController extends AbstractController


{
    #[Route('/admin/SecurityDevController', name: 'app_admin_security_dev')]
    public function index(Request $request): Response
    {
        // Hier wird die Session überprüft
        if ($request->query->get('reset')) {
            $request->getSession()->remove('fingerprintData');
            $request->getSession()->remove('rawPostData');
            $request->getSession()->remove('errorMessage');
            $this->addFlash('success', 'Fingerprint-Session zurückgesetzt');
            return $this->redirectToRoute('app_admin_security_dev');
        }
        
        $fingerprintData = $request->getSession()->get('fingerprintData', []);
    
        $rawPostData = $request->getSession()->get('rawPostData', null);
        $errorMessage = $request->getSession()->get('errorMessage', null);
    
        return $this->render('dev/SecurityDevController.html.twig', [
            'fingerprint' => $fingerprintData,
            'raw_post' => $rawPostData,
            'error' => $errorMessage,
        ]);
    }

    #[Route('/admin/security-dev/fingerprint', name: 'app_admin_security_fingerprint', methods: ['POST'])]
    public function collect(Request $request): JsonResponse
    {
        try {
            $json = $request->getContent();
            $data = json_decode($json, true);
    
            $request->getSession()->set('rawPostData', $json);
    
            if (!$data) {
                $request->getSession()->set('errorMessage', 'JSON konnte nicht dekodiert werden.');
                return new JsonResponse(['status' => 'invalid_json'], 400);
            }
    
            $request->getSession()->set('fingerprintData', $data);
            $request->getSession()->remove('errorMessage');
    
            return new JsonResponse(['status' => 'success']);
        } catch (\Throwable $e) {
            $request->getSession()->set('errorMessage', $e->getMessage());
            return new JsonResponse(['status' => 'fatal_error'], 500);
        }
    }


}
