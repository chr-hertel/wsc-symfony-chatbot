<?php

declare(strict_types=1);

namespace App\OpenAI;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GptClient implements GptClientInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $openAiApiKey,
    ) {
    }

    public function generateResponse(array $messages): string
    {
        $body = [
            'model' => 'gpt-4o',
            'messages' => $messages,
            'temperature' => 1.0,
        ];

        $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
            'auth_bearer' => $this->openAiApiKey,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($body),
        ]);

        return $response->toArray()['choices'][0]['message']['content'];
    }
}
