<?php

declare(strict_types=1);

namespace App\WscProgram\Data;

final class Slot
{
    private function __construct(
        public \DateTimeImmutable $start,
        public ?\DateTimeImmutable $end = null,
    ) {
    }

    public static function fromTimeRange(\DateTimeImmutable $date, string $timeRange): self
    {
        $dateString = $date->format('Y-m-d');
        $times = explode(' - ', $timeRange);

        if (2 < count($times)) {
            throw new \InvalidArgumentException('Invalid time range');
        }

        $startDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $dateString.' '.$times[0], $date->getTimezone());

        if (false === $startDate) {
            throw new \InvalidArgumentException('Invalid start date');
        }

        $endDate = null;
        if (isset($times[1])) {
            $endDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $dateString.' '.$times[1], $date->getTimezone());

            if (false === $endDate) {
                throw new \InvalidArgumentException('Invalid end date');
            }
        }

        return new self($startDate, $endDate);
    }

    public static function fromStrings(string $start, ?string $param): self
    {
        $start = new \DateTimeImmutable($start);
        $end = null;

        if (null !== $param) {
            $end = new \DateTimeImmutable($param);
        }

        return new self($start, $end);
    }
}
