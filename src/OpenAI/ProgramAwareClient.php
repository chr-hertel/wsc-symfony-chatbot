<?php

declare(strict_types=1);

namespace App\OpenAI;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Filesystem\Filesystem;

// #[AsDecorator(decorates: GptClientInterface::class, priority: 10)]
final class ProgramAwareClient implements GptClientInterface
{
    public function __construct(
        private GptClientInterface $gptClient,
        private string $programFile,
    ) {
    }

    public function generateResponse(array $messages): string
    {
        $program = (new Filesystem())->readFile($this->programFile);
        $systemPrompt = <<<PROMPT
            You are a helpful assistant for visitors of the Web Summer Camp and answer questions about
            the program of the event. It takes place in Opatija, Croatia from 4th of July until 6th.
            It starts with two days of workshops and ends with a conference day. The workshops are
            dedicated to Symfony, JavaScript, PHP and UX.
            
            {$program}
            PROMPT;

        array_unshift($messages, ['role' => 'system', 'content' => $systemPrompt]);

        return $this->gptClient->generateResponse($messages);
    }
}
