<?php
// src/Command/WebhookTestCommand.php
namespace App\Command;

use App\Service\WebhookService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:webhook:test',
    description: 'Sendet einen Test-Webhook.'
)]
class WebbhookTestCommand extends Command
{
    public function __construct(
        private readonly WebhookService $webhookService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->webhookService->send(
            '✅ Webhook-Test erfolgreich 🎉',
            [
                'Nachricht' => 'Dieser Ping wurde erfolgreich an Discord gesendet.',
                'Zeit' => (new \DateTime())->format('Y-m-d H:i:s'),
                'Server' => gethostname(),
            ],
            [
                'color' => 3066993, // grün
                'username' => 'WebhookBot 🤖',
                'avatar_url' => 'https://cdn-icons-png.flaticon.com/512/4712/4712109.png'
            ]
        );

        $output->writeln('<info>📡 Webhook wurde gesendet ✅</info>');

        return Command::SUCCESS;
    }
}
