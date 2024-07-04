<?php

declare(strict_types=1);

namespace App\WscProgram\Data;

final class Schedule
{
    /**
     * @param list<Day> $days
     */
    public function __construct(
        public readonly Track $track,
        public readonly array $days,
    ) {
    }

    public function findSlot(string $title): Slot
    {
        foreach ($this->days as $day) {
            try {
                return $day->findSlot($title);
            } catch (\DomainException) {
                continue;
            }
        }

        throw new \DomainException('Title not found in schedule');
    }
}
