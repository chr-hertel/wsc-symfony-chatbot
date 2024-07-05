<?php

declare(strict_types=1);

namespace App\Twig;

use App\Wikipedia;
use PhpLlm\LlmChain\Message\Role;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('wikipedia')]
final class WikipediaComponent
{
    use DefaultActionTrait;

    public function __construct(
        private readonly Wikipedia $chat,
    ) {
    }

    /**
     * @phpstan-return MessageList
     */
    public function getMessages(): array
    {
        $messages = [];
        foreach ($this->chat->loadMessages() as $message) {
            if (Role::System === $message->role) {
                continue;
            }

            $messages[] = [
                'role' => $message->role->value,
                'content' => $message->content ?? '',
            ];
        }

        return $messages;
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
