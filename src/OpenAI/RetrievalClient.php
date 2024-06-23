<?php

declare(strict_types=1);

namespace App\OpenAI;

use App\WscProgram\Data\Session;
use Codewithkyrian\ChromaDB\Client;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: GptClientInterface::class, priority: 10)]
final class RetrievalClient implements GptClientInterface
{
    public function __construct(
        private EmbeddingClient $embeddingClient,
        private readonly Client $chromaClient,
        private readonly GptClientInterface $gptClient,
    ) {
    }

    public function generateResponse(array $messages): string
    {
        $prompt = <<<PROMPT
            You are an helpful assistant answering questions in a chat based on the information provided by the assistant
            messages in the conversation. If you can't find the answer, just say so.
            
            You can also answer questions about the program of the Web Summer Camp 2024, which is an in-depth conference
            for professionals from around the world, organized since 2012, with a focus on practical learning and the
            sharing of experiences among peers. This year it takes place from July 4th to 6th in Opatija, Croatia. The
            first two days are dedicated to hands-on workshops, while the third day is reserved for conference talks.
            PROMPT;

        array_unshift($messages, ['role' => 'system', 'content' => $prompt]);

        $userMessage = array_pop($messages);

        try {
            $retrievalString = $this->getRetrievalInformation($userMessage['content']);
            $messages[] = ['role' => 'assistant', 'content' => $retrievalString];
        } catch (\Exception) {
        }

        $messages[] = $userMessage;

        return $this->gptClient->generateResponse($messages);
    }

    private function getRetrievalInformation(string $message): string
    {
        $vector = $this->embeddingClient->create($message);
        $collection = $this->chromaClient->getOrCreateCollection('wsc-program');
        $queryResponse = $collection->query(
            queryEmbeddings: [$vector],
            nResults: 4,
        );

        if (1 === count($queryResponse->ids, COUNT_RECURSIVE)) {
            throw new \Exception('No results found');
        }

        $retrievalString = 'Additional Information:'.PHP_EOL;
        /* @phpstan-ignore-next-line */
        foreach ($queryResponse->metadatas[0] as $metadata) {
            /* @phpstan-ignore-next-line */
            $retrievalString .= Session::fromArray($metadata)->toString().PHP_EOL;
        }

        return $retrievalString;
    }
}
