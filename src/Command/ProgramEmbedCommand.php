<?php

declare(strict_types=1);

namespace App\Command;

use App\WscProgram\Embedder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:program:embed', description: 'Create embeddings for program data and push to ChromaDB.')]
final class ProgramEmbedCommand extends Command
{
    public function __construct(
        private readonly ?Embedder $embedder,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Creating embeddings for program data and push to ChromaDB');

        if (null === $this->embedder) {
            $io->error('Embedder is not implemented yet!');

            return Command::FAILURE;
        }

        $this->embedder->embedProgram();

        $io->success('Program Data Successfully Embedded!');

        return Command::SUCCESS;
    }
}
