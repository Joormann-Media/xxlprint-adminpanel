<?php
// src/Controller/MobileVerificationController.php
namespace App\Controller;

use App\Service\SmsSenderService;
use App\Service\MobileVerificationTokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MobileVerificationController extends AbstractController
{
    // src/Controller/MobileVerificationController.php

#[Route('/verify-mobile', name: 'app_verify_mobile')]
public function verifyMobile(
    Request $request,
    SmsSenderService $smsSender,
    MobileVerificationTokenManager $tokenManager,
    
    EntityManagerInterface $em
): Response {
    $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException();
    }

    // === 1. MOBILNUMMER FORMULAR ===
    $mobileForm = $this->createFormBuilder()
        ->add('mobile', TextType::class, [
            'label' => 'Mobilnummer eingeben',
            'data' => $user->getMobile(),
            'attr' => ['placeholder' => '+49 151 ...'],
        ])
        ->add('send', SubmitType::class, ['label' => '📤 Code senden'])
        ->getForm();

    $mobileForm->handleRequest($request);

    if ($mobileForm->isSubmitted() && $mobileForm->isValid()) {
        $mobileNumber = $mobileForm->get('mobile')->getData();

        try {
            $code = $tokenManager->createToken($user); // DB statt Session!
            $smsSender->sendSms($mobileNumber, "Dein Bestätigungscode lautet: {$code}");

            $user->setMobile($mobileNumber);
            $user->setMobileVerified(false);
            $em->flush();

            $this->addFlash('success', 'Code wurde per SMS gesendet.');
        } catch (\Throwable $e) {
            $this->addFlash('error', 'SMS konnte nicht gesendet werden: ' . $e->getMessage());
        }
    }

    // === 2. CODE VALIDIERUNG ===
    $codeForm = $this->createFormBuilder()
        ->add('code', TextType::class, [
            'label' => 'Bestätigungscode',
            'attr' => ['placeholder' => '6-stelliger Code'],
        ])
        ->add('verify', SubmitType::class, ['label' => '✅ Verifizieren'])
        ->getForm();

    $codeForm->handleRequest($request);

    if ($codeForm->isSubmitted() && $codeForm->isValid()) {
        $inputCode = $codeForm->get('code')->getData();

        if ($tokenManager->validateToken($user, $inputCode)) {
            $user->setMobileVerified(true);
            $em->flush();
            $this->addFlash('success', 'Mobilnummer erfolgreich verifiziert.');
            return $this->redirectToRoute('app_verify_mobile');
        } else {
            $this->addFlash('error', 'Der eingegebene Code ist ungültig oder abgelaufen.');
        }
    }

    return $this->render('mobile_verification/verify.html.twig', [
        'mobileForm' => $mobileForm->createView(),
        'codeForm' => $codeForm->createView(),
        'mobileVerified' => $user->isMobileVerified(),
    ]);
}

}
