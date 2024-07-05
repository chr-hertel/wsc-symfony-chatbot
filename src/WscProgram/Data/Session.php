<?php

declare(strict_types=1);

namespace App\WscProgram\Data;

use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type SessionData array{
 *     title: string,
 *     description: string,
 *     start: string,
 *     end: ?string,
 *     speaker: string,
 *     jobTitle: string,
 *     bio: string,
 *     track: string
 * }
 */
final class Session
{
    public Uuid $id;

    public function __construct(
        public string $title,
        public string $description,
        public Slot $slot,
        public string $speaker,
        public string $jobTitle,
        public string $bio,
        public Track $track,
    ) {
        $this->id = Uuid::v4();
    }

    /**
     * @phpstan-param SessionData $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['description'],
            Slot::fromStrings($data['start'], $data['end'] ?? null),
            $data['speaker'],
            $data['jobTitle'],
            $data['bio'],
            Track::from($data['track']),
        );
    }

    /**
     * @phpstan-return SessionData
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'start' => $this->slot->start->format('Y-m-d H:i:s'),
            'end' => $this->slot->end?->format('Y-m-d H:i:s'),
            'speaker' => $this->speaker,
            'jobTitle' => $this->jobTitle,
            'bio' => $this->bio,
            'track' => $this->track->value,
        ];
    }

    public function toString(): string
    {
        return sprintf(
            'Title: %s, Description: %s, Date: %s - %s, Speaker: %s, Job Title: %s, Bio: %s, Track: %s',
            $this->title, $this->description, $this->slot->start->format('Y-m-d H:i:s'), $this->slot->end?->format('Y-m-d H:i:s'), $this->speaker, $this->jobTitle, $this->bio, $this->track->value
        );
    }
}
