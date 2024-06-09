<?php

declare(strict_types=1);

namespace App\OpenAI;

use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: GptClientInterface::class)]
final class DateTimeAwareClient implements GptClientInterface
{
    public function __construct(
        private readonly GptClientInterface $gptClient,
        private readonly ClockInterface $clock,
    ) {
    }

    public function generateResponse(array $messages): string
    {
        $now = $this->clock->now();
        $prompt = sprintf(
            'Current date is %s (YYYY-MM-DD) and the time is %s (HH:MM:SS).',
            $now->format('Y-m-d'),
            $now->format('H:i:s'),
        );

        array_unshift($messages, ['role' => 'assistant', 'content' => $prompt]);

        return $this->gptClient->generateResponse($messages);
    }
}
