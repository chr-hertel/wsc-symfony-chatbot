<?php

declare(strict_types=1);

namespace App;

use App\OpenAI\GptClientInterface;
use App\YouTube\TranscriptFetcher;
use PhpLlm\LlmChain\ChatChain;
use PhpLlm\LlmChain\Message\Message;
use PhpLlm\LlmChain\Message\MessageBag;
use PhpLlm\LlmChain\ToolChain;
use Symfony\Component\HttpFoundation\RequestStack;

final class YouTube
{
    private const SESSION_KEY = 'youtube-chat';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ChatChain $toolChain,
        private readonly TranscriptFetcher $transcriptFetcher,
    ) {
    }

    public function loadMessages(): MessageBag
    {
        return $this->requestStack->getSession()->get(self::SESSION_KEY, new MessageBag());
    }

    public function start(string $videoId): void
    {
        $this->reset();
        $messages = $this->loadMessages();

        $transcript = $this->transcriptFetcher->fetchTranscript($videoId);
        $system = <<<PROMPT
            You are an helpful assistant that answers questions about a YouTube video based on a transcript.
            If you can't answer a question, say so.
            
            Transcript:
            {$transcript}
            PROMPT;

        $messages[] = Message::forSystem($system);
        $messages[] = Message::ofAssistant('What do you want to know about that video?');

        $this->saveMessages($messages);
    }

    public function submitMessage(string $message): void
    {
        $messages = $this->loadMessages();

        $message = Message::ofUser($message);
        $response = $this->toolChain->call($message, $messages);

        $messages[] = $message;
        $messages[] = Message::ofAssistant($response);

        $this->saveMessages($messages);
    }

    public function reset(): void
    {
        $this->requestStack->getSession()->remove(self::SESSION_KEY);
    }

    private function saveMessages(MessageBag $messages): void
    {
        $this->requestStack->getSession()->set(self::SESSION_KEY, $messages);
    }
}
