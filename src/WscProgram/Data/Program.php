<?php

declare(strict_types=1);

namespace App\WscProgram\Data;

final class Program
{
    /**
     * @param list<Schedule> $schedules
     * @param list<Session>  $sessions
     */
    public function __construct(
        public array $schedules,
        public array $sessions = [],
    ) {
    }

    /**
     * @return array{track: Track, slot: Slot}
     */
    public function findSlot(string $title): array
    {
        foreach ($this->schedules as $schedule) {
            try {
                return ['track' => $schedule->track, 'slot' => $schedule->findSlot($title)];
            } catch (\DomainException) {
                continue;
            }
        }

        throw new \DomainException(sprintf('Title "%s" not found in program', $title));
    }

    /**
     * @param list<Session> $sessions
     */
    public function withSessions(array $sessions): self
    {
        $program = clone $this;
        $program->sessions = $sessions;

        return $program;
    }
}
