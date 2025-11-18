<?php

namespace App\Command;

use App\Service\DiscordWebhookService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'discord:webhook:send',
    description: 'Sendet eine Nachricht an den Discord-Webhook'
)]
class DiscordWebhookCommand extends Command
{
    private DiscordWebhookService $discordWebhookService;

    public function __construct(DiscordWebhookService $discordWebhookService)
    {
        parent::__construct();
        $this->discordWebhookService = $discordWebhookService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('message', InputArgument::REQUIRED, 'Nachricht für Discord');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $message = $input->getArgument('message');

        $ok = $this->discordWebhookService->send($message);

        if ($ok) {
            $io->success('Nachricht erfolgreich an Discord geschickt!');
            return Command::SUCCESS;
        } else {
            $io->error('Fehler beim Senden an Discord (Webhook-URL korrekt?)');
            return Command::FAILURE;
        }
    }
}
