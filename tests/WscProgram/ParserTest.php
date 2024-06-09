<?php

declare(strict_types=1);

namespace App\Tests\WscProgram;

use App\WscProgram\Data\Day;
use App\WscProgram\Data\Program;
use App\WscProgram\Data\Schedule;
use App\WscProgram\Data\Session;
use App\WscProgram\Data\Slot;
use App\WscProgram\Data\Track;
use App\WscProgram\Parser;
use Nyholm\NSA;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parser::class)]
final class ParserTest extends TestCase
{
    #[DataProvider('provideScheduleFiles')]
    public function testParseSchedule(string $file, Track $track, Schedule $expected): void
    {
        $html = file_get_contents($file);

        if (false === $html) {
            self::fail(sprintf('Failed to read schedule file "%s"', $file));
        }

        $parser = new Parser();
        $actual = $parser->parseSchedule($track, $html);

        self::assertEquals($expected, $actual);
    }

    public static function provideScheduleFiles(): \Generator
    {
        $day1 = new \DateTimeImmutable('2024-07-03');
        $day2 = new \DateTimeImmutable('2024-07-04');
        $day3 = new \DateTimeImmutable('2024-07-05');
        $day4 = new \DateTimeImmutable('2024-07-06');

        yield 'symfony' => [
            __DIR__.'/fixtures/schedule-symfony.html',
            Track::SymfonyWorkshops,
            new Schedule(Track::SymfonyWorkshops, [
                new Day($day1, [
                    ['slot' => Slot::fromTimeRange($day1, '19:00'), 'event' => 'Welcome drink'],
                ]),
                new Day($day2, [
                    ['slot' => Slot::fromTimeRange($day2, '8:30 - 10:00'), 'event' => 'Registration & coffee'],
                    ['slot' => Slot::fromTimeRange($day2, '9:50 - 10:00'), 'event' => 'The opening shortest keynote ever'],
                    ['slot' => Slot::fromTimeRange($day2, '10:00 - 12:00'), 'event' => 'Testing Symfony applications with Codeception'],
                    ['slot' => Slot::fromTimeRange($day2, '12:00 - 13:15'), 'event' => 'Lunch'],
                    ['slot' => Slot::fromTimeRange($day2, '13:15 - 15:15'), 'event' => 'Demystify Symfony - Understanding the functions of the framework'],
                    ['slot' => Slot::fromTimeRange($day2, '15:15 - 15:30'), 'event' => 'Coffee break'],
                    ['slot' => Slot::fromTimeRange($day2, '15:30 - 17:30'), 'event' => 'Demystify Symfony - Understanding the functions of the framework'],
                    ['slot' => Slot::fromTimeRange($day2, '17:30 - 19:30'), 'event' => 'Beer & BBQ hangout'],
                ]),
                new Day($day3, [
                    ['slot' => Slot::fromTimeRange($day3, '8:00 - 8:30'), 'event' => 'Morning recreation'],
                    ['slot' => Slot::fromTimeRange($day3, '9:00 - 10:00'), 'event' => 'Registration & coffee'],
                    ['slot' => Slot::fromTimeRange($day3, '10:00 - 12:00'), 'event' => 'Enjoying frontend with Symfony UX'],
                    ['slot' => Slot::fromTimeRange($day3, '12:00 - 13:15'), 'event' => 'Lunch'],
                    ['slot' => Slot::fromTimeRange($day3, '13:15 - 15:15'), 'event' => 'Custom Chatbots with GPT and Symfony PHP framework'],
                    ['slot' => Slot::fromTimeRange($day3, '15:15 - 15:30'), 'event' => 'Coffee break'],
                    ['slot' => Slot::fromTimeRange($day3, '15:30 - 17:30'), 'event' => 'Custom Chatbots with GPT and Symfony PHP framework'],
                    ['slot' => Slot::fromTimeRange($day3, '19:30 - 20:30'), 'event' => 'Dinner'],
                    ['slot' => Slot::fromTimeRange($day3, '21:00'), 'event' => 'Beach hangout'],
                ]),
            ]),
        ];

        yield 'conference' => [
            __DIR__.'/fixtures/schedule-conference.html',
            Track::ConferenceTalks,
            new Schedule(Track::ConferenceTalks, [
                new Day($day4, [
                    ['slot' => Slot::fromTimeRange($day4, '8:00 - 8:30'), 'event' => 'Morning recreation'],
                    ['slot' => Slot::fromTimeRange($day4, '9:00 - 10:00'), 'event' => 'Registration & coffee'],
                    ['slot' => Slot::fromTimeRange($day4, '10:00 - 12:00'), 'event' => 'The 100 year web, Democratizing Design for Better Collaboration, The Unreliable Computer Revolution: Embracing the Double-Edged Sword of GenAI'],
                    ['slot' => Slot::fromTimeRange($day4, '12:00 - 13:15'), 'event' => 'Lunch'],
                    ['slot' => Slot::fromTimeRange($day4, '13:15 - 15:15'), 'event' => 'Core Web Vitals under control, Modernizing Legacy Apps: Platforms and Strategies, DX, UX, UI: Things You Didn’t Notice & How To Fix Them'],
                    ['slot' => Slot::fromTimeRange($day4, '15:15 - 15:30'), 'event' => 'Coffee break'],
                    ['slot' => Slot::fromTimeRange($day4, '15:30 - 17:30'), 'event' => 'Stop using JavaScript for that: moving features from JS to CSS and HTML, Supercharge your coding: Integrating ChatGPT and Copilot in long-term projects, Evolutionary Architecture: the art of making decisions'],
                    ['slot' => Slot::fromTimeRange($day4, '17:30 - 19:30'), 'event' => 'Leisure time'],
                    ['slot' => Slot::fromTimeRange($day4, '19:30 - 20:30'), 'event' => 'Dinner'],
                    ['slot' => Slot::fromTimeRange($day4, '21:00'), 'event' => 'Closing party'],
                ]),
            ]),
        ];
    }

    #[DataProvider('provideSessionFiles')]
    public function testParseSession(string $file, Session $expected): void
    {
        $program = new Program([
            new Schedule(Track::BusinessTrack, [
                new Day(new \DateTimeImmutable('2024-07-05'), [['event' => 'Staying ahead... What are the successful service business doing? What should you be doing?', 'slot' => Slot::fromTimeRange(new \DateTimeImmutable('2024-07-05'), '15:30 - 17:30')]]),
            ]),
            new Schedule(Track::ConferenceTalks, [
                new Day(new \DateTimeImmutable('2024-07-06'), [['event' => 'Core Web Vitals under control', 'slot' => Slot::fromTimeRange(new \DateTimeImmutable('2024-07-06'), '13:15 - 15:15')]]),
            ]),
        ]);

        $html = file_get_contents($file);

        if (false === $html) {
            self::fail(sprintf('Failed to read session file "%s"', $file));
        }

        $parser = new Parser();
        $actual = $parser->parseSession($html, $program);

        // Disrespect the ID ¯\_(ツ)_/¯
        NSA::setProperty($expected, 'id', $actual->id);

        self::assertEquals($expected, $actual);
    }

    public static function provideSessionFiles(): \Generator
    {
        yield 'talk' => [
            __DIR__.'/fixtures/session-talk.html',
            new Session(
                'Core Web Vitals under control',
                'Core Web Vitals is a universal set of user-centric metrics initiated by Google that allows us to analyse, qualify and optimise for the quality of user experience.In my talk, I will tell you which metrics build this set and which factors impact their results. Besides, I will show you tools to measure and debug Core Web Vitals. And last but not least, I will put the presented theory into practice and demonstrate the good practices to improve Web Vitals metrics in your project.Enjoy and keep Core Web Vitals under control!',
                Slot::fromTimeRange(new \DateTimeImmutable('2024-07-06'), '13:15 - 15:15'),
                'Marta Wiśniewska',
                'Angular & Web Google Developer Expert',
                'Google Developer Expert in Angular and Web Technologies from Poland. An international speaker and blogger. Passionate about Angular, PWA, and web tech trends. Enjoys sharing knowledge and fostering community engagement in technology.',
                Track::ConferenceTalks,
            ),
        ];

        yield 'workshop' => [
            __DIR__.'/fixtures/session-workshop.html',
            new Session(
                'Staying ahead... What are the successful service business doing? What should you be doing?',
                'What are the high-performers doing right now? What can you do to catch up or stay ahead of the game? In this engaging workshop, we\'ll explore what needs to be done!Robert will be sharing his experience of working with over 100 agencies right across Europe.   We\'ll discuss:how good is your agency?what are the high performers doing differently?what should you be doing, right now?Coming out of the workshop, you will have a better understanding of how some agencies are doing better and how you can join them. The workshop will be fast-paced and interactive.',
                Slot::fromTimeRange(new \DateTimeImmutable('2024-07-05'), '15:30 - 17:30'),
                'Robert Craven',
                'Director @ Grow Your Digital Agency',
                'Known for his no-nonsense approach to business growth, Robert has worked with agencies and e-comm businesses from Bucharest and Split to Slovenia, from London to Dublin, from Warsaw to Kiev, from Singapore to New York.',
                Track::BusinessTrack,
            ),
        ];
    }
}
