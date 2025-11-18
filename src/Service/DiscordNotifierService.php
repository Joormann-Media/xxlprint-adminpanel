<?php

namespace App\Service;

use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

class DiscordNotifierService
{
    public function __construct(private readonly ChatterInterface $chatter)
    {
    }

    public function sendNotification(string $message): void
    {
        $chatMessage = new ChatMessage($message);

        // Optionale Anpassungen (Embeds, etc.) können hier hinzugefügt werden
        $discordOptions = (new DiscordOptions())
            ->username('Symfony Bot')
            ->avatarUrl('https://symfony.com/logos/symfony_black_03.svg');
            // ->addEmbed((new DiscordEmbed()) ...); // Beispiel für Embeds

        $chatMessage->options($discordOptions);

        $this->chatter->send($chatMessage);
    }
}
