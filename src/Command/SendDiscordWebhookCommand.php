<?php

namespace App\Command;

use App\Service\DiscordWebhookService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendDiscordWebhookCommand extends Command
{
    protected static $defaultName = 'webhook:discord:send';

    private DiscordWebhookService $discord;

    public function __construct(DiscordWebhookService $discord)
    {
        parent::__construct('app:send-discord-webhook');
        $this->discord = $discord;
    }

    protected function configure()
    {
        $this
            ->setDescription('Sendet eine Nachricht an Discord via Webhook')
            ->addArgument('message', InputArgument::REQUIRED, 'Nachricht an Discord');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = $input->getArgument('message');

        if ($this->discord->send($message)) {
            $output->writeln('<info>Nachricht erfolgreich gesendet!</info>');
            return Command::SUCCESS;
        }

        $output->writeln('<error>Senden fehlgeschlagen!</error>');
        return Command::FAILURE;
    }
}
