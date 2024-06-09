<?php

declare(strict_types=1);

namespace App\WscProgram\Data;

final class Day
{
    /**
     * @param list<array{event: string, slot: Slot}> $sessions
     */
    public function __construct(
        public \DateTimeImmutable $date,
        public array $sessions,
    ) {
    }

    public function findSlot(string $title): Slot
    {
        foreach ($this->sessions as $session) {
            if (str_contains($session['event'], $title)) {
                return $session['slot'];
            }
        }

        throw new \DomainException('Title not found in day');
    }
}
