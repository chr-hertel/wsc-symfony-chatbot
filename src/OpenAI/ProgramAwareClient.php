<?php

declare(strict_types=1);

namespace App\OpenAI;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Filesystem\Filesystem;

#[AsDecorator(decorates: GptClientInterface::class, priority: 10)]
final class ProgramAwareClient implements GptClientInterface
{
    public function __construct(
        private readonly GptClientInterface $gptClient,
        private readonly string $programFile,
    ) {
    }

    public function generateResponse(array $messages): string
    {
        $program = (new Filesystem())->readFile($this->programFile);
        $prompt = <<<PROMPT
            You are an helpful assistant answering questions in a chat based on the information provided by the assistant
            messages in the conversation. If you can't find the answer, just say so.
            
            You can also answer questions about the program of the Web Summer Camp 2024, which is an in-depth conference
            for professionals from around the world, organized since 2012, with a focus on practical learning and the
            sharing of experiences among peers. This year it takes place from July 4th to 6th in Opatija, Croatia. The
            first two days are dedicated to hands-on workshops, while the third day is reserved for conference talks.

            {$program}
            PROMPT;

        array_unshift($messages, ['role' => 'system', 'content' => $prompt]);

        return $this->gptClient->generateResponse($messages);
    }
}
