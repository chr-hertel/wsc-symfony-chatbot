<?php

declare(strict_types=1);

namespace App\Twig;

use App\YouTube;
use PhpLlm\LlmChain\Message\Role;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;

use function Symfony\Component\String\u;

#[AsLiveComponent('youtube')]
final class YouTubeComponent
{
    use DefaultActionTrait;

    public function __construct(
        private readonly YouTube $youTube,
    ) {
    }

    #[LiveAction]
    public function start(#[LiveArg] string $videoId): void
    {
        if (str_contains($videoId, 'youtube.com')) {
            $videoId = $this->getVideoIdFromUrl($videoId);
        }

        try {
            $this->youTube->start($videoId);
        } catch (\Exception) {
            $this->youTube->reset();
        }
    }

    /**
     * @phpstan-return MessageList
     */
    public function getMessages(): array
    {
        $messages = [];
        foreach ($this->youTube->loadMessages() as $message) {
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
        $this->youTube->submitMessage($message);
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->youTube->reset();
    }

    private function getVideoIdFromUrl(string $url): string
    {
        $query = parse_url($url, PHP_URL_QUERY);

        if (!$query) {
            throw new \InvalidArgumentException('Unable to parse YouTube URL.');
        }

        return u($query)->after('v=')->before('&')->toString();
    }
}
