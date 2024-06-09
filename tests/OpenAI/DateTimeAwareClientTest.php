<?php

declare(strict_types=1);

namespace App\Tests\OpenAI;

use App\OpenAI\DateTimeAwareClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

#[CoversClass(DateTimeAwareClient::class)]
final class DateTimeAwareClientTest extends TestCase
{
    use ClockSensitiveTrait;

    public function testProgramGetsPrepended(): void
    {
        $innerClient = new MockClient(
            assertion: function (array $messages) {
                self::assertCount(2, $messages, 'Expected two messages');
                self::assertSame('assistant', $messages[0]['role'], 'Expected assistant message to be first message');
                $expected = 'Current date is 2024-06-22 (YYYY-MM-DD) and the time is 12:34:56 (HH:MM:SS).';
                self::assertStringContainsString($expected, $messages[0]['content'], 'Expected the defined message format.');
                self::assertSame('user', $messages[1]['role'], 'Expected user message to be second message');
                self::assertSame('How many days until the Web Summer Camp starts?', $messages[1]['content'], 'Expected original user message as content.');
            },
            response: 'There are still 12 days left.',
        );
        $client = new DateTimeAwareClient($innerClient, $this->mockTime(new \DateTimeImmutable('2024-06-22 12:34:56')));

        $messages = [['role' => 'user', 'content' => 'How many days until the Web Summer Camp starts?']];
        $response = $client->generateResponse($messages);

        self::assertSame('There are still 12 days left.', $response);
    }

    public function testSystemMessageGetsPrependedOnlyOnce(): void
    {
        $innerClient = new MockClient(
            assertion: function (array $messages) {
                self::assertCount(4, $messages, 'Expected five messages');
                self::assertSame('assistant', $messages[0]['role'], 'Expected assistant message to be first message');
            },
            response: 'There are still 12 days left.',
        );
        $client = new DateTimeAwareClient($innerClient, $this->mockTime(new \DateTimeImmutable('2024-06-22 12:34:56')));

        $messages = [
            ['role' => 'user', 'content' => 'Which date do we have today?'],
            ['role' => 'assistant', 'content' => 'Today is June 22, 2024.'],
            ['role' => 'user', 'content' => 'How many days until the Web Summer Camp starts?'],
        ];
        $response = $client->generateResponse($messages);

        self::assertSame('There are still 12 days left.', $response);
    }
}
