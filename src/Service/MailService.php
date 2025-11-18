<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailService
{
    public function __construct(private readonly MailerInterface $mailer) {}

    public function sendPinResetEmail(User $user, string $token): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@joormann-media.de', 'Admin Panel'))
            ->to($user->getEmail())
            ->subject('PIN zurücksetzen')
            ->htmlTemplate('emails/pin_reset.html.twig')
            ->context([
                'user' => $user,
                'token' => $token,
            ]);

        $this->mailer->send($email);
    }

    public function sendPasswordResetEmail(User $user, string $token): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@joormann-media.de', 'Admin Panel'))
            ->to($user->getEmail())
            ->subject('Passwort zurücksetzen')
            ->htmlTemplate('emails/password_reset.html.twig')
            ->context([
                'user' => $user,
                'token' => $token,
            ]);

        $this->mailer->send($email);
    }
}
