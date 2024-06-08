<?php

declare(strict_types=1);

namespace App\Tests\OpenAI;

use App\OpenAI\GptClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[CoversClass(GptClient::class)]
final class GptClientTest extends TestCase
{
    public function testGenerateResponseHappyCase(): void
    {
        $expectedRequest = [
            function ($method, $url, $options): MockResponse {
                self::assertSame('POST', $method);
                self::assertSame('https://api.openai.com/v1/chat/completions', $url);
                self::assertArrayHasKey('headers', $options);
                self::assertTrue(in_array('Content-Type: application/json', $options['headers'], true), 'Content-Type header is missing');
                self::assertTrue(in_array('Authorization: Bearer api-key', $options['headers'], true), 'Authorization header is missing');
                self::assertArrayHasKey('body', $options);
                self::assertJson($options['body']);
                self::assertStringContainsString('"model":"gpt-4o"', $options['body'], 'Model is not set to "gpt-4o" or missing');
                self::assertStringContainsString('"messages":[{', $options['body'], 'Messages array is missing');
                self::assertStringContainsString('{"role":"user","content":"Hi, my name is Jane!"}', $options['body'], 'User message is missing');
                self::assertStringContainsString('"temperature":', $options['body'], 'Temperature is missing');

                return MockResponse::fromFile(dirname(__DIR__).'/fixtures/gpt-response.json');
            },
        ];

        $httpClient = new MockHttpClient($expectedRequest);
        $client = new GptClient($httpClient, 'api-key');

        $response = $client->generateResponse([['role' => 'user', 'content' => 'Hi, my name is Jane!']]);

        self::assertSame('Hello, Jane! How can I assist you today?', $response);
    }
}
