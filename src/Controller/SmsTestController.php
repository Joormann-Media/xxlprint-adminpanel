<?php
namespace App\Controller;

use App\Service\TwilioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SmsTestController extends AbstractController
{
    #[Route('/api/test/sms', name: 'api_test_sms')]
    public function testSms(TwilioService $twilio): JsonResponse
    {
        $to = '+49176xxxxxxx'; // 👉 deine Testnummer (verifiziert bei Twilio)
        $text = '🚀 Symfony 7 sagt Hallo via Twilio!';

        $success = $twilio->sendSms($to, $text);

        return $this->json(['success' => $success]);
    }
}
