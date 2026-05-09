<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserName(): void
    {
        $user = new User();

        $user->setName('ina');

        $this->assertEquals('ina', $user->getName());
    }

    public function testUserEmail(): void
    {
        $user = new User();

        $user->setEmail('ina@test.com');

        $this->assertEquals('ina@test.com', $user->getEmail());
    }

    public function testAdminRole(): void
    {
        $user = new User();

        $user->setAdmin(true);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
    }

    public function testUserIsActive(): void
    {
        $user = new User();

        $user->setIsActive(true);

        $this->assertTrue($user->isActive());
    }

    public function testUserPassword(): void
    {
        $user = new User();

        $user->setPassword('password');

        $this->assertEquals('password', $user->getPassword());
    }
}