<?php

declare(strict_types=1);

namespace App\WscProgram;

use App\WscProgram\Data\Program;
use App\WscProgram\Data\Schedule;
use App\WscProgram\Data\Sitemap;
use App\WscProgram\Data\Track;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Loader
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer,
        private Parser $parser,
        private LoggerInterface $logger,
    ) {
    }

    public function loadProgram(): Program
    {
        // loading all schedules
        $schedules = $this->loadSchedules();
        $program = new Program($schedules);

        // loading website urls from sitemap
        $response = $this->httpClient->request('GET', 'https://websummercamp.com/sitemap.xml');
        $sitemap = $this->serializer->deserialize($response->getContent(), Sitemap::class, 'xml');

        // initiate requests for program urls
        $responses = [];
        foreach ($sitemap->getUrls() as $url) {
            if (!$url->isProgramUrl()) {
                continue;
            }
            $responses[] = $this->httpClient->request('GET', $url->getLocation());
        }

        // parse slots from responses
        $sessions = [];
        foreach ($responses as $response) {
            try {
                $sessions[] = $this->parser->parseSession($response->getContent(), $program);
            } catch (\DomainException $e) {
                $this->logger->warning($e->getMessage());
                continue;
            }
        }

        return $program->withSessions($sessions);
    }

    /**
     * @return list<Schedule>
     */
    private function loadSchedules(): array
    {
        $schedules = [];
        $uxResponse = $this->httpClient->request('GET', 'https://websummercamp.com/2024/program/ux');
        $schedules[] = $this->parser->parseSchedule(Track::UxWorkshops, $uxResponse->getContent());
        $bizResponse = $this->httpClient->request('GET', 'https://websummercamp.com/2024/program/biz');
        $schedules[] = $this->parser->parseSchedule(Track::BusinessTrack, $bizResponse->getContent());
        $jsResponse = $this->httpClient->request('GET', 'https://websummercamp.com/2024/program/js');
        $schedules[] = $this->parser->parseSchedule(Track::JavascriptWorkshops, $jsResponse->getContent());
        $phpResponse = $this->httpClient->request('GET', 'https://websummercamp.com/2024/program/php');
        $schedules[] = $this->parser->parseSchedule(Track::PhpWorkshops, $phpResponse->getContent());
        $symfonyResponse = $this->httpClient->request('GET', 'https://websummercamp.com/2024/program/symfony');
        $schedules[] = $this->parser->parseSchedule(Track::SymfonyWorkshops, $symfonyResponse->getContent());
        $conferenceResponse = $this->httpClient->request('GET', 'https://websummercamp.com/2024/program/conf');
        $schedules[] = $this->parser->parseSchedule(Track::ConferenceTalks, $conferenceResponse->getContent());

        return $schedules;
    }
}
