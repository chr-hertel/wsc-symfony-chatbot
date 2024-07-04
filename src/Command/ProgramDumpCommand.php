<?php

declare(strict_types=1);

namespace App\Command;

use App\WscProgram\Dumper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:program:dump', description: 'Dumping program data into file.')]
final class ProgramDumpCommand extends Command
{
    public function __construct(
        private readonly Dumper $dumper,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Dumping Program Data of Web Summer Camp');

        $this->dumper->dumpProgram();

        $io->success('Program Data Dumped Successfully!');

        return Command::SUCCESS;
    }
}
