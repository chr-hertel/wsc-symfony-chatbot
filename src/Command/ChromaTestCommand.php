<?php

declare(strict_types=1);

namespace App\Command;

use App\OpenAI\EmbeddingClient;
use Codewithkyrian\ChromaDB\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:test:chroma', description: 'Testing Chroma DB connection.')]
final class ChromaTestCommand extends Command
{
    public function __construct(
        private readonly Client $chromaClient,
        private readonly ?EmbeddingClient $embeddingClient,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Testing Chroma DB Connection');

        $io->comment('Connecting to Chroma DB ...');

        // Check current ChromaDB version
        $version = $this->chromaClient->version();

        // Get WSC Collection
        $collection = $this->chromaClient->getOrCreateCollection('wsc-program');

        $io->table(['Key', 'Value'], [
            ['ChromaDB Version', $version],
            ['Collection Name', $collection->name],
            ['Collection ID', $collection->id],
            ['Total Documents', $collection->count()],
        ]);

        if (null === $this->embeddingClient) {
            $io->error('EmbeddingClient is not implemented yet!');

            return Command::FAILURE;
        }

        $io->comment('Searching for Symfony content ...');

        $symfonyVector = $this->embeddingClient->create('Session about Symfony');
        $queryResponse = $collection->query(
            queryEmbeddings: [$symfonyVector],
            nResults: 4,
        );

        if (1 === count($queryResponse->ids, COUNT_RECURSIVE)) {
            $io->error('No results found!');

            return Command::FAILURE;
        }

        $io->table(['ID', 'Title'], [
            /* @phpstan-ignore-next-line */
            [$queryResponse->ids[0][0], $queryResponse->metadatas[0][0]['title']],
            /* @phpstan-ignore-next-line */
            [$queryResponse->ids[0][1], $queryResponse->metadatas[0][1]['title']],
            /* @phpstan-ignore-next-line */
            [$queryResponse->ids[0][2], $queryResponse->metadatas[0][2]['title']],
            /* @phpstan-ignore-next-line */
            [$queryResponse->ids[0][3], $queryResponse->metadatas[0][3]['title']],
        ]);

        $io->success('Chroma DB Connection & Similarity Search Test Successful!');

        return Command::SUCCESS;
    }
}
