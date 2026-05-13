<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class LoginFormAuthenticatorTest extends TestCase
{
    public function testAuthenticateReturnsPassport(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $userRepository = $this->createMock(UserRepository::class);

        $authenticator = new LoginFormAuthenticator($urlGenerator, $userRepository);

        $request = new Request([], [
            '_username' => 'ina@test.com',
            '_password' => 'password',
            '_csrf_token' => 'csrf-token',
        ]);

        $request->setSession(new Session(new MockArraySessionStorage()));

        $passport = $authenticator->authenticate($request);

        $this->assertNotNull($passport);
    }

    public function testAuthenticateThrowsExceptionWhenUserDoesNotExist(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $userRepository = $this->createMock(UserRepository::class);

        $userRepository
            ->method('findOneBy')
            ->willReturn(null);

        $authenticator = new LoginFormAuthenticator($urlGenerator, $userRepository);

        $request = new Request([], [
            '_username' => 'missing@test.com',
            '_password' => 'password',
            '_csrf_token' => 'csrf-token',
        ]);

        $request->setSession(new Session(new MockArraySessionStorage()));

        $passport = $authenticator->authenticate($request);

        $userBadge = $passport->getBadge(\Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge::class);

        $this->expectException(CustomUserMessageAuthenticationException::class);

        $userBadge->getUser();
    }

    public function testAuthenticateThrowsExceptionWhenUserIsBlocked(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $userRepository = $this->createMock(UserRepository::class);

        $blockedUser = new User();
        $blockedUser->setEmail('blocked@test.com');
        $blockedUser->setName('blocked');
        $blockedUser->setPassword('password');
        $blockedUser->setIsActive(false);

        $userRepository
            ->method('findOneBy')
            ->willReturn($blockedUser);

        $authenticator = new LoginFormAuthenticator($urlGenerator, $userRepository);

        $request = new Request([], [
            '_username' => 'blocked@test.com',
            '_password' => 'password',
            '_csrf_token' => 'csrf-token',
        ]);

        $request->setSession(new Session(new MockArraySessionStorage()));

        $passport = $authenticator->authenticate($request);

        $userBadge = $passport->getBadge(\Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge::class);

        $this->expectException(CustomUserMessageAuthenticationException::class);

        $userBadge->getUser();
    }

    public function testOnAuthenticationSuccessRedirectsToAdminGuests(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $userRepository = $this->createMock(UserRepository::class);

        $urlGenerator
            ->method('generate')
            ->with('admin_guest_index')
            ->willReturn('/admin/guests');

        $authenticator = new LoginFormAuthenticator($urlGenerator, $userRepository);

        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $token = $this->createMock(TokenInterface::class);

        $response = $authenticator->onAuthenticationSuccess($request, $token, 'main');

        $this->assertSame('/admin/guests', $response->getTargetUrl());
    }

    public function testGetLoginUrlReturnsLoginRoute(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $userRepository = $this->createMock(UserRepository::class);

        $urlGenerator
            ->method('generate')
            ->with('admin_login')
            ->willReturn('/login');

        $authenticator = new LoginFormAuthenticator($urlGenerator, $userRepository);

        $reflection = new \ReflectionClass($authenticator);
        $method = $reflection->getMethod('getLoginUrl');
        $method->setAccessible(true);

        $url = $method->invoke($authenticator, new Request());

        $this->assertSame('/login', $url);
    }
}