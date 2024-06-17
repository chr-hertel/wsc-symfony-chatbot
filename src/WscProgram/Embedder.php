<?php

declare(strict_types=1);

namespace App\WscProgram;

use App\OpenAI\EmbeddingClient;
use Codewithkyrian\ChromaDB\Client;

final class Embedder
{
    public function __construct(
        private readonly Loader $loader,
        private readonly EmbeddingClient $embeddingClient,
        private readonly Client $chromaClient,
    ) {
    }

    public function embedProgram(): void
    {
        $collection = $this->chromaClient->getOrCreateCollection('wsc-program');

        $ids = [];
        $embeddings = [];
        $metadatas = [];
        foreach ($this->loader->loadProgram()->sessions as $session) {
            $ids[] = $session->id;
            $embeddings[] = $this->embeddingClient->create($session->toString());
            $metadatas[] = $session->toArray();
        }

        $collection->add($ids, $embeddings, $metadatas);
    }
}
