<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpClientFactory
{
    public static function createMock(): HttpClientInterface
    {
        return new MockHttpClient(
            fn () => MockResponse::fromFile(__DIR__.'/fixtures/gpt-response.json'),
        );
    }
}
