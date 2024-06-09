<?php

declare(strict_types=1);

namespace App\Tests;

use App\Twig\ChatComponent;
use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\TraceableHttpClient;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;

#[CoversNothing]
final class SmokeTest extends WebTestCase
{
    use InteractsWithLiveComponents;

    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.card-header strong', 'Chat with Symfony & GPT');
        self::assertSelectorExists('h4', 'Welcome to your Chat Bot with Symfony & GPT!');
    }

    public function testSubmitEndWithCorrectOrderOfMessages(): void
    {
        $client = static::createClient();
        $component = $this->createLiveComponent(ChatComponent::class, [], $client);
        $component->render();

        $client->enableProfiler();
        $component->call('submit', ['message' => 'How many days until Web Summer Camp starts?']);

        self::assertResponseIsSuccessful();

        /** @var TraceableHttpClient $tracedClient */
        $tracedClient = self::getContainer()->get('TraceableHttpClient');
        $requests = $tracedClient->getTracedRequests();

        self::assertCount(1, $requests);
        self::assertNotNull($requests[0]['options']['body'] ?? null);

        $data = json_decode($requests[0]['options']['body'], true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(3, $data['messages']);
        self::assertSame('system', $data['messages'][0]['role'], 'The first message should be the system prompt');
        self::assertSame('assistant', $data['messages'][1]['role'], 'The second message should be the assistant prompt');
        self::assertSame('user', $data['messages'][2]['role'], 'The third message should be the user prompt');
    }
}
