<?php

declare(strict_types=1);

namespace App;

use App\OpenAI\GptClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class Chat
{
    private const SESSION_KEY = 'chat-messages';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly GptClientInterface $gptClient,
    ) {
    }

    /**
     * @phpstan-return MessageList
     */
    public function loadMessages(): array
    {
        return $this->requestStack->getSession()->get(self::SESSION_KEY, []);
    }

    public function submitMessage(string $message): void
    {
        $messages = $this->loadMessages();

        $messages[] = ['role' => 'user', 'content' => $message];
        $response = $this->gptClient->generateResponse($messages);
        $messages[] = ['role' => 'assistant', 'content' => $response];

        $this->saveMessages($messages);
    }

    public function reset(): void
    {
        $this->requestStack->getSession()->remove(self::SESSION_KEY);
    }

    /**
     * @phpstan-param MessageList $messages
     */
    private function saveMessages(array $messages): void
    {
        $this->requestStack->getSession()->set(self::SESSION_KEY, $messages);
    }
}
