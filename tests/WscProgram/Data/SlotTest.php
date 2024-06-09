<?php

declare(strict_types=1);

namespace App\Tests\WscProgram\Data;

use App\WscProgram\Data\Slot;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Slot::class)]
final class SlotTest extends TestCase
{
    public function testSlotWithStartAndEnd(): void
    {
        $slot = Slot::fromTimeRange(new \DateTimeImmutable('2023-06-08 00:00:00'), '09:00 - 10:00');

        self::assertEquals(new \DateTimeImmutable('2023-06-08 09:00'), $slot->start);
        self::assertEquals(new \DateTimeImmutable('2023-06-08 10:00'), $slot->end);
    }

    public function testSlotWithStartOnly(): void
    {
        $slot = Slot::fromTimeRange(new \DateTimeImmutable('2023-06-08 00:00:00'), '19:00');

        self::assertEquals(new \DateTimeImmutable('2023-06-08 19:00'), $slot->start);
        self::assertNull($slot->end);
    }

    public function testSlotWithInvalidTime(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid time range');

        Slot::fromTimeRange(new \DateTimeImmutable('2023-06-08 00:00:00'), '19:00 - 20:00 - 21:00');
    }

    public function testSlotWithInvalidStartTime(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid start date');

        Slot::fromTimeRange(new \DateTimeImmutable('2023-06-08 00:00:00'), 'invalid');
    }

    public function testSlotWithInvalidEndTime(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid end date');

        Slot::fromTimeRange(new \DateTimeImmutable('2023-06-08 00:00:00'), '19:00 - invalid');
    }
}
