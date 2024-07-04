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
}
