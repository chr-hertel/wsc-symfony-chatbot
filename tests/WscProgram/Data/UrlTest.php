<?php

declare(strict_types=1);

namespace App\Tests\WscProgram\Data;

use App\WscProgram\Data\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Url::class)]
final class UrlTest extends TestCase
{
    #[DataProvider('provideValidProgramUrls')]
    public function testIsValidProgramUrl(string $location): void
    {
        $url = new Url();
        $url->setLocation($location);

        self::assertTrue($url->isProgramUrl());
    }

    /**
     * @return array<string[]>
     */
    public static function provideValidProgramUrls(): array
    {
        return [
            ['https://websummercamp.com/workshop/enjoying-frontend-with-symfony-ux'],
            ['https://websummercamp.com/talk/the-100-year-web'],
            ['https://websummercamp.com/talk/modernizing-legacy-apps-platforms-and-strategies'],
            ['https://websummercamp.com/workshop/accelerating-journey-mapping-with-ai'],
        ];
    }

    #[DataProvider('provideInvalidProgramUrls')]
    public function testIsInvalidProgramUrl(string $location): void
    {
        $url = new Url();
        $url->setLocation($location);

        self::assertFalse($url->isProgramUrl());
    }

    /**
     * @return array<string[]>
     */
    public static function provideInvalidProgramUrls(): array
    {
        return [
            ['https://websummercamp.com/'],
            ['https://websummercamp.com/speakers/dino-esposito'],
            ['https://websummercamp.com/media/menu-items/wsc2024'],
            ['https://websummercamp.com/interview'],
            ['https://websummercamp.com/interview/growing-and-selling-your-business.-how-when-and-why'],
            ['https://websummercamp.com/tickets'],
            ['https://websummercamp.com/news'],
            ['https://websummercamp.com/2023/workshop/netgen-layouts'],
        ];
    }
}
