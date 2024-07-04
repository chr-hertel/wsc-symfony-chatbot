<?php

declare(strict_types=1);

namespace App\Tests\OpenAI;

use App\OpenAI\GptClientInterface;

final class MockClient implements GptClientInterface
{
    public function __construct(
        private \Closure $assertion,
        private string $response,
    ) {
    }

    public function generateResponse(array $messages): string
    {
        ($this->assertion)($messages);

        return $this->response;
    }
}
