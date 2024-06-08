<?php

declare(strict_types=1);

namespace App\OpenAI;

interface GptClientInterface
{
    /**
     * @phpstan-param MessageList $messages
     */
    public function generateResponse(array $messages): string;
}
