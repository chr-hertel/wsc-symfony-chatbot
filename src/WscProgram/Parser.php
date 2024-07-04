<?php

declare(strict_types=1);

namespace App\WscProgram;

use App\WscProgram\Data\Day;
use App\WscProgram\Data\Program;
use App\WscProgram\Data\Schedule;
use App\WscProgram\Data\Session;
use App\WscProgram\Data\Slot;
use App\WscProgram\Data\Track;
use Symfony\Component\DomCrawler\Crawler as SymfonyCrawler;

use function Symfony\Component\String\u;

final readonly class Parser
{
    public function parseSchedule(Track $track, string $html): Schedule
    {
        $crawler = new SymfonyCrawler($html);

        return new Schedule(
            track: $track,
            days: $crawler->filter('#workshopSchedule h2')->each(function (SymfonyCrawler $day) {
                $date = new \DateTimeImmutable(u($day->text())->before(' - ')->toString());

                return new Day(
                    date: $date,
                    sessions: $day->nextAll()->filter('table')->eq(0)->filter('tr')->each(function (SymfonyCrawler $session) use ($date) {
                        $topics = $session->filter('td')->eq(1);
                        $event = 0 < $topics->filter('strong')->count()
                            ? u($topics->filter('strong')->text())->trim()->toString()
                            : implode(', ', $topics->filter('h3')->each(fn (SymfonyCrawler $topic) => $topic->text()));

                        return [
                            'event' => $event,
                            'slot' => Slot::fromTimeRange($date, u($session->filter('td')->eq(0)->text())->trim()->replace('19-30', '19:30')->toString()),
                        ];
                    }),
                );
            }),
        );
    }

    public function parseSession(string $html, Program $program): Session
    {
        $crawler = new SymfonyCrawler($html);
        $title = $crawler->filter('h1')->text();

        ['track' => $track, 'slot' => $slot] = $program->findSlot($title);

        return new Session(
            title: $title,
            description: u($crawler->filter('.full-page-body .ezrichtext-field')->text())->ascii()->trim()->toString(),
            slot: $slot,
            speaker: $crawler->filter('.view-type-circle_with_intro.circle h2.title')->text(),
            jobTitle: u($crawler->filter('.view-type-circle_with_intro.circle div.function')->text())->ascii()->toString(),
            bio: $crawler->filter('.view-type-circle_with_intro.circle div.short')->text(),
            track: $track,
        );
    }
}
