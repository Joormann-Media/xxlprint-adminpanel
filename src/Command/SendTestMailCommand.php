<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:send-testmail',
    description: 'Sendet eine Test-E-Mail zur Prüfung der Mailer-Konfiguration.',
)]
class SendTestMailCommand extends Command
{
    public function __construct(private readonly MailerInterface $mailer)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('noreplay@xxl-print-wesel.de')
            ->to('joormann@gmx.de') // ✨ Hier DEINE Zieladresse eintragen!
            ->subject('Test-E-Mail von XXL-Print Admin')
            ->text('Dies ist eine Testmail, gesendet über den Symfony-Command weils geil ist.')
            ->html('<p><strong>Dies ist eine Testmail</strong> über den Symfony-Command weil sonst alles klemmt2.</p>');

        try {
            $this->mailer->send($email);
            $output->writeln('<info>✅ Testmail erfolgreich verschickt!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>❌ Fehler beim Versand der Mail: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
