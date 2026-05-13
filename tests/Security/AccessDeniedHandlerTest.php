<?php

namespace App\Tests\Security;

use App\Security\AccessDeniedHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedHandlerTest extends TestCase
{
    public function testHandleReturnsRedirectResponse(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $urlGenerator
            ->method('generate')
            ->willReturn('/');

        $handler = new AccessDeniedHandler($urlGenerator);

        $response = $handler->handle(
            new Request(),
            new AccessDeniedException()
        );

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
}