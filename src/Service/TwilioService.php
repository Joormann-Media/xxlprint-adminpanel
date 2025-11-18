<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Twilio\Rest\Client;

class TwilioService
{
    private Client $twilio;
    private string $from;
    private LoggerInterface $logger;

    public function __construct(
        string $sid,
        string $token,
        string $from,
        LoggerInterface $logger
    ) {
        $this->twilio = new Client($sid, $token);
        $this->from = $from;
        $this->logger = $logger;
    }

    public function sendSms(string $to, string $message): bool
    {
        try {
            $this->twilio->messages->create($to, [
                'from' => $this->from,
                'body' => $message,
            ]);

            $this->logger->info("[TwilioService] SMS erfolgreich an {$to} gesendet.");
            return true;
        } catch (\Throwable $e) {
            $this->logger->error("[TwilioService] Fehler beim Senden: " . $e->getMessage());
            return false;
        }
    }
}
