<?php

declare(strict_types=1);

namespace App\Tests\WscProgram\Data;

use App\WscProgram\Data\Session;
use App\WscProgram\Data\Slot;
use App\WscProgram\Data\Track;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Session::class)]
final class SessionTest extends TestCase
{
    private Session $session;

    protected function setUp(): void
    {
        $this->session = new Session(
            title: 'Building Space Rockets only with HTML & CSS',
            description: 'With my new awesome CSS framework it is possible to fly to the moon.',
            slot: Slot::fromTimeRange(new \DateTimeImmutable('2063-12-29'), '14:15 - 16:17'),
            speaker: 'Loin Mosque',
            jobTitle: 'Supi Dupi Developer',
            bio: 'I like to write code and eat snickers.',
            track: Track::UxWorkshops,
        );
    }

    public function testToArray(): void
    {
        $expected = [
            'title' => 'Building Space Rockets only with HTML & CSS',
            'description' => 'With my new awesome CSS framework it is possible to fly to the moon.',
            'start' => '2063-12-29 14:15:00',
            'end' => '2063-12-29 16:17:00',
            'speaker' => 'Loin Mosque',
            'jobTitle' => 'Supi Dupi Developer',
            'bio' => 'I like to write code and eat snickers.',
            'track' => 'UX Workshops',
        ];

        self::assertSame($expected, $this->session->toArray());
    }

    public function testToString(): void
    {
        $expected = ''
            .'Title: Building Space Rockets only with HTML & CSS, '
            .'Description: With my new awesome CSS framework it is possible to fly to the moon., '
            .'Date: 2063-12-29 14:15:00 - 2063-12-29 16:17:00, '
            .'Speaker: Loin Mosque, '
            .'Job Title: Supi Dupi Developer, '
            .'Bio: I like to write code and eat snickers., '
            .'Track: UX Workshops';

        self::assertSame($expected, $this->session->toString());
    }
}
