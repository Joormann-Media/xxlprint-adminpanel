<?php
// src/Command/UpdateDigestHashCommand.php
namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:set-digest')]
class UpdateDigestHashCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Setzt den Digest-Hash für einen User')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('realm', InputArgument::OPTIONAL, 'Realm für Digest', 'Mein DAV Server');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $realm = $input->getArgument('realm');

        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $output->writeln("<error>User '$username' nicht gefunden.</error>");
            return Command::FAILURE;
        }

        $digest = md5("$username:$realm:$password");
        $user->setDigestHash($digest);
        $this->em->flush();

        $output->writeln("<info>Digest-Hash gesetzt für '$username': $digest</info>");

        return Command::SUCCESS;
    }
}
