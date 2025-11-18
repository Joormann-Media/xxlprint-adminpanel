<?php

namespace App\Command;

use App\Repository\UserSessionRepository;
use App\Repository\SessionTableRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:sessions:cleanup',
    description: 'Bereinigt abgelaufene UserSessions und Symfony-Sessions.'
)]
class CleanupUserSessionsCommand extends Command
{
    public function __construct(
        private readonly UserSessionRepository $userSessionRepo,
        private readonly SessionTableRepository $sessionTableRepo,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Nur anzeigen, was gelöscht würde')
            ->addOption('older-than', null, InputOption::VALUE_REQUIRED, 'Nur Sessions löschen, die älter sind (z. B. "10m", "2h", "1d")');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dryRun = $input->getOption('dry-run');
        $olderThan = $input->getOption('older-than');
        $cutoff = null;

        if ($olderThan) {
            $cutoff = $this->parseOlderThan($olderThan);
            if (!$cutoff) {
                $output->writeln('<error>❌ Ungültiges Format für --older-than. Erlaubt: 10m, 2h, 1d</error>');
                return Command::INVALID;
            }
            $output->writeln("📆 Sessions älter als: <comment>" . $cutoff->format('Y-m-d H:i:s') . "</comment>");
        }

        if ($dryRun) {
            $userSessions = $this->userSessionRepo->findExpiredSessions($cutoff);
            $symfonySessions = $this->sessionTableRepo->findExpiredSessions($cutoff);

            $output->writeln("\n<info>🧪 Dry-Run aktiv – keine Sessions werden gelöscht</info>");
            $output->writeln("👤 UserSessions: <comment>" . count($userSessions) . "</comment>");
            $output->writeln("🧠 Symfony-Sessions: <comment>" . count($symfonySessions) . "</comment>");

            return Command::SUCCESS;
        }

        $countUserSessions = $this->userSessionRepo->deleteExpiredSessions($cutoff);
        $countSymfonySessions = $this->sessionTableRepo->deleteExpiredSessions($cutoff);

        $output->writeln("\n<info>🧹 Cleanup ausgeführt</info>");
        $output->writeln("✅ $countUserSessions UserSession(s) gelöscht.");
        $output->writeln("✅ $countSymfonySessions Symfony-Session(s) gelöscht.");

        return Command::SUCCESS;
    }

    private function parseOlderThan(string $input): ?DateTime
    {
        if (!preg_match('/^(\d+)([mhd])$/', $input, $matches)) {
            return null;
        }

        [$full, $value, $unit] = $matches;

        $now = new DateTime();

        return match ($unit) {
            'm' => $now->sub(new DateInterval("PT{$value}M")),
            'h' => $now->sub(new DateInterval("PT{$value}H")),
            'd' => $now->sub(new DateInterval("P{$value}D")),
            default => null,
        };
    }
}
