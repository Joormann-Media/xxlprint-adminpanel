<?php

namespace App\Command;

use App\Service\DiscordNotifierService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-discord-webhook',
    description: 'Sendet eine Testnachricht an den Discord Webhook.',
)]
class TestDiscordWebhookCommand extends Command
{
    public function __construct(private readonly DiscordNotifierService $discordNotifier)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('message', InputArgument::OPTIONAL, 'Die zu sendende Nachricht', 'Dies ist eine Testnachricht von meinem Symfony 7 Projekt!');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $message = $input->getArgument('message');

        try {
            $this->discordNotifier->sendNotification($message);
            $io->success('Die Testnachricht wurde erfolgreich an Discord gesendet.');
        } catch (\Exception $e) {
            $io->error('Fehler beim Senden der Nachricht: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}