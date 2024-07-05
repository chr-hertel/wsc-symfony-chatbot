<?php

declare(strict_types=1);

namespace App\Tool;

use App\OpenAI\EmbeddingClient;
use App\WscProgram\Data\Session;
use Codewithkyrian\ChromaDB\Client;
use PhpLlm\LlmChain\ToolBox\AsTool;

#[AsTool('retriever', description: 'Retrieves information from the Web Summer Camp program.')]
final class Retriever
{
    public function __construct(
        private EmbeddingClient $embeddingClient,
        private Client $chromaClient,
    ) {
    }

    /**
     * @param string $searchTerm string used to search in the WSC program for
     */
    public function __invoke(string $searchTerm): string
    {
        $vector = $this->embeddingClient->create($searchTerm);
        $collection = $this->chromaClient->getOrCreateCollection('wsc-program');
        $queryResponse = $collection->query(
            queryEmbeddings: [$vector],
            nResults: 4,
        );

        if (1 === count($queryResponse->ids, COUNT_RECURSIVE)) {
            throw new \Exception('No results found');
        }

        $retrievalString = 'Information from Web Summer Camp Program:'.PHP_EOL;
        /* @phpstan-ignore-next-line */
        foreach ($queryResponse->metadatas[0] as $metadata) {
            /* @phpstan-ignore-next-line */
            $retrievalString .= Session::fromArray($metadata)->toString().PHP_EOL;
        }

        return $retrievalString;
    }
}
