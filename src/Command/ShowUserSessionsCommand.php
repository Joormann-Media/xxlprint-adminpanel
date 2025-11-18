<?php

namespace App\Command;

use App\Repository\UserSessionRepository;
use App\Repository\SessionTableRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:sessions:show',
    description: 'Zeigt aktive User- und Symfony-Sessions mit optionaler Filterung.'
)]
class ShowUserSessionsCommand extends Command
{
    public function __construct(
        private readonly UserSessionRepository $userSessionRepo,
        private readonly SessionTableRepository $sessionTableRepo,
        private readonly MailerInterface $mailer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Anzahl der Einträge begrenzen', 20)
            ->addOption('trusted-only', null, InputOption::VALUE_NONE, 'Nur vertrauenswürdige Sessions anzeigen')
            ->addOption('notify', null, InputOption::VALUE_NONE, 'Warnung bei verdächtigen IPs ausgeben')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'E-Mail-Adresse für Benachrichtigung (z. B. admin@example.com)')
            ->addOption('silent', null, InputOption::VALUE_NONE, 'Nur Zählwerte anzeigen (still, ideal für Cron)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = (int) $input->getOption('limit');
        $trustedOnly = $input->getOption('trusted-only');
        $silent = $input->getOption('silent');
        $notify = $input->getOption('notify');
        $emailTarget = $input->getOption('email');

        $userSessions = $this->userSessionRepo->findRecentSessions($limit, $trustedOnly);
        $symfonySessions = $this->sessionTableRepo->findActiveSessions($limit);

        $suspicious = [];

        if ($silent) {
            $output->writeln("👤 UserSessions: " . count($userSessions));
            $output->writeln("🧠 Symfony-Sessions: " . count($symfonySessions));
            return Command::SUCCESS;
        }

        $output->writeln("\n<info>👤 Benutzer-Sessions (UserSessions):</info>");
        foreach ($userSessions as $s) {
            $status = $s->isActive() ? '✅' : '❌';
            $trusted = $s->getIsTrusted() ? '🟢' : '🔴';
            $line = sprintf(
                '%s [%s] %s (%s) @ %s',
                $status,
                $trusted,
                $s->getUser()?->getUsername() ?? '—',
                $s->getIp(),
                $s->getLastActiveAt()?->format('Y-m-d H:i:s') ?? '—'
            );
            $output->writeln($line);

            if ($notify && !$s->getIsTrusted()) {
                $msg = "🚨 Verdächtige IP: {$s->getIp()} (User: {$s->getUser()?->getUsername()})";
                $suspicious[] = $msg;
                $output->writeln("<error>$msg</error>");
            }
        }

        $output->writeln("\n<info>🧠 Symfony-Sessions (sessions-Tabelle):</info>");
        foreach ($symfonySessions as $s) {
            $output->writeln(sprintf(
                '%s @ %s (user_id: %s)',
                $s['sess_id'],
                date('Y-m-d H:i:s', $s['sess_time']),
                $s['user_id'] ?? '—'
            ));
        }

        // Optional: E-Mail-Versand bei verdächtigen IPs
        if (!empty($suspicious) && $emailTarget) {
            $email = (new Email())
                ->from('no-reply@system.local')
                ->to($emailTarget)
                ->subject('🛡️ Session-Warnung: Verdächtige IPs erkannt')
                ->text("Folgende verdächtige Zugriffe wurden erkannt:\n" . implode("\n", $suspicious));

            $this->mailer->send($email);
            $output->writeln("\n📧 Warnung an {$emailTarget} gesendet.");
        }

        return Command::SUCCESS;
    }
}