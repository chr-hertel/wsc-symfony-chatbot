<?php

declare(strict_types=1);

namespace App\WscProgram;

use App\OpenAI\EmbeddingClient;
use Codewithkyrian\ChromaDB\Client;
use Symfony\Component\Uid\Uuid;

final class Embedder
{
    public function __construct(
        private Loader $loader,
        private EmbeddingClient $embeddingClient,
        private Client $chromaDBClient,
    ) {
    }

    public function embedProgram(): void
    {
        $program = $this->loader->loadProgram();

        $ids = [];
        $vectors = [];
        $metadatas = [];
        foreach ($program->sessions as $session) {
            $ids[] = Uuid::v4()->toRfc4122();
            $vectors[] = $this->embeddingClient->create($session->toString());
            $metadatas[] = $session->toArray();
        }

        $collection = $this->chromaDBClient->getOrCreateCollection('wsc-program');
        $collection->add($ids, $vectors, $metadatas);
    }
}
