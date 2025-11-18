<?php

// src/Controller/SmsController.php
namespace App\Controller;

use App\Form\SmsType;
use App\Service\SmsSenderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SmsController extends AbstractController
{
    #[Route('/sms/send', name: 'app_sms_send')]
    public function send(Request $request, SmsSenderService $smsSender): Response
    {
        $form = $this->createForm(SmsType::class);
        $form->handleRequest($request);
        $result = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            try {
                $result = $smsSender->sendSms($data['recipient'], $data['message']);
                $this->addFlash('success', 'SMS erfolgreich versendet!');
            } catch (\Throwable $e) {
                $this->addFlash('danger', 'Fehler beim Senden der SMS: ' . $e->getMessage());
            }
        }

        return $this->render('sms/send.html.twig', [
            'form' => $form->createView(),
            'result' => $result,
        ]);
    }
}
