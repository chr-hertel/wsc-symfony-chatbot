<?php

declare(strict_types=1);

namespace App\Twig;

use App\Chat;
use PhpLlm\LlmChain\Message\MessageBag;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('chat')]
final class ChatComponent
{
    use DefaultActionTrait;

    public function __construct(
        private readonly Chat $chat,
    ) {
    }

    /**
     * @phpstan-return MessageList
     */
    public function getMessages(): array
    {
        $messageBag = $this->chat->loadMessages();
        /** @phpstan-ignore-next-line Only needed as compat layer between challenges */
        $messages = $messageBag instanceof MessageBag ? $messageBag->toArray() : $messageBag;

        /* @phpstan-ignore-next-line Only needed as compat layer between challenges */
        return array_values(
            array_filter($messages, fn ($message) => 'system' !== $message['role'])
        );
    }

    #[LiveAction]
    public function submit(#[LiveArg] string $message): void
    {
        $this->chat->submitMessage($message);
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->chat->reset();
    }
}
