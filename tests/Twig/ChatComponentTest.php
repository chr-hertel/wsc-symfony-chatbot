<?php

declare(strict_types=1);

namespace App\Tests\Twig;

use App\Twig\ChatComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

#[CoversClass(ChatComponent::class)]
final class ChatComponentTest extends KernelTestCase
{
    private ChatComponent $chatComponent;

    protected function setUp(): void
    {
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        /** @var RequestStack $requestStack */
        $requestStack = self::getContainer()->get(RequestStack::class);
        $requestStack->push($request);

        /** @var ChatComponent $chatComponent */
        $chatComponent = self::getContainer()->get(ChatComponent::class);
        $this->chatComponent = $chatComponent;
    }

    public function testGetInitialMessages(): void
    {
        $messages = $this->chatComponent->getMessages();

        self::assertIsArray($messages);
        self::assertCount(0, $messages);
    }

    public function testSubmitMessage(): void
    {
        $this->chatComponent->submit('Hi, my name is Jane!');

        $expected = [
            ['role' => 'user', 'content' => 'Hi, my name is Jane!'],
            ['role' => 'assistant', 'content' => 'Hello, Jane! How can I assist you today?'],
        ];

        $actual = $this->chatComponent->getMessages();

        self::assertIsArray($actual);
        self::assertCount(2, $actual);
        self::assertSame($expected, $actual);
    }

    public function testReset(): void
    {
        $this->chatComponent->submit('Hi, my name is Jane!');
        $this->chatComponent->reset();

        $messages = $this->chatComponent->getMessages();

        self::assertIsArray($messages);
        self::assertCount(0, $messages);
    }
}
