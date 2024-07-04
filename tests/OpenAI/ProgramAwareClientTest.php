<?php

declare(strict_types=1);

namespace App\Tests\OpenAI;

use App\OpenAI\ProgramAwareClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProgramAwareClient::class)]
final class ProgramAwareClientTest extends TestCase
{
    public function testProgramGetsPrepended(): void
    {
        $innerClient = new MockClient(
            assertion: function (array $messages) {
                self::assertCount(2, $messages, 'Expected two messages');
                self::assertSame('system', $messages[0]['role'], 'Expected system message to be first message');
                $systemPrompt = $messages[0]['content'];
                self::assertStringContainsString('Democratizing Design for Better Collaboration', $systemPrompt, 'Expected program to be part of the system prompt.');
                self::assertStringContainsString('Opatija', $systemPrompt, 'Expected Opatija named as location in system prompt.');
                self::assertSame('user', $messages[1]['role'], 'Expected user message to be second message');
                self::assertSame('Can I learn something about UX and Figma?', $messages[1]['content'], 'Expected original user message as content.');
            },
            response: 'Yes, you can learn about UX and Figma. There is a workshop about it.',
        );
        $client = new ProgramAwareClient($innerClient, dirname(__DIR__, 2).'/wsc-program.txt');

        $messages = [['role' => 'user', 'content' => 'Can I learn something about UX and Figma?']];
        $response = $client->generateResponse($messages);

        self::assertSame('Yes, you can learn about UX and Figma. There is a workshop about it.', $response);
    }

    public function testSystemMessageGetsPrependedOnlyOnce(): void
    {
        $innerClient = new MockClient(
            assertion: function (array $messages) {
                self::assertCount(4, $messages, 'Expected five messages');
                self::assertSame('system', $messages[0]['role'], 'Expected system message to be first message');
            },
            response: 'The workshop about UX and Figma will be led by Oana Stroe.',
        );
        $client = new ProgramAwareClient($innerClient, dirname(__DIR__, 2).'/wsc-program.txt');

        $messages = [
            ['role' => 'user', 'content' => 'Can I learn something about UX and Figma?'],
            ['role' => 'assistant', 'content' => 'Yes, you can learn about UX and Figma. There is a workshop about it.'],
            ['role' => 'user', 'content' => 'Who is doing a workshop about it?'],
        ];
        $response = $client->generateResponse($messages);

        self::assertSame('The workshop about UX and Figma will be led by Oana Stroe.', $response);
    }
}
