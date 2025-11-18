<?php
// src/Command/AddressNormalizeCommand.php

namespace App\Command;

use App\Entity\OfficialAddress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'address:normalize',
    description: 'Normalisiert alle bestehenden Adress-Datensätze (füllt das normalized-Feld)',
)]
class AddressNormalizeCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
{
    $io = new SymfonyStyle($input, $output);

    $repo = $this->em->getRepository(OfficialAddress::class);

    $countAll = $repo->count([]);
    $io->progressStart($countAll);

    $qb = $repo->createQueryBuilder('a');
    $query = $qb->getQuery();
    $iterable = $query->toIterable();

    $batchSize = 100;
    $i = 0;
    $changed = 0;

    foreach ($iterable as $address) {
        /** @var OfficialAddress $address */
        $oldNorm = $address->getNormalized();
        $address->updateNormalized();
        if ($address->getNormalized() !== $oldNorm) {
            $changed++;
        }

        if (($i > 0) && ($i % $batchSize === 0)) {
            $this->em->flush();
            $this->em->clear();
        }

        $io->progressAdvance();
        $i++;
    }

    // Noch offene Änderungen flushen
    $this->em->flush();
    $this->em->clear();

    $io->progressFinish();
    $io->success("Alle bestehenden Adressen ($changed geändert) wurden normalisiert.");

    return Command::SUCCESS;
}

}
