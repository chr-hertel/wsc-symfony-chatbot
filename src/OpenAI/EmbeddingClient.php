<?php

declare(strict_types=1);

namespace App\OpenAI;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class EmbeddingClient
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $openAiApiKey,
    ) {
    }

    /**
     * @return list<float>
     */
    public function create(string $text): array
    {
        $body = [
            'model' => 'text-embedding-ada-002',
            'input' => $text,
        ];

        $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/embeddings', [
            'auth_bearer' => $this->openAiApiKey,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($body),
        ]);

        return $response->toArray()['data'][0]['embedding'];
    }
}
