<?php

declare(strict_types=1);

namespace App;

use PhpLlm\LlmChain\Message\Message;
use PhpLlm\LlmChain\Message\MessageBag;
use PhpLlm\LlmChain\ToolChain;
use Symfony\Component\HttpFoundation\RequestStack;

final class Wikipedia
{
    private const SESSION_KEY = 'wikipedia-chat-messages';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ToolChain $toolChain,
    ) {
    }

    public function loadMessages(): MessageBag
    {
        $default = new MessageBag();
        $default[] = Message::forSystem('Please answer the users question based on Wikipedia and provide a link to the article.');

        return $this->requestStack->getSession()->get(self::SESSION_KEY, $default);
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
