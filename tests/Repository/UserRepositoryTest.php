<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends KernelTestCase
{
    public function testFindUserByEmail(): void
    {
        self::bootKernel();

        $repository = static::getContainer()->get(UserRepository::class);

        $user = $repository->findOneBy([
            'email' => 'ina@test.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('ina@test.com', $user->getEmail());
    }

    public function testUpgradePasswordUpdatesUserPassword(): void
    {
        self::bootKernel();

        $repository = static::getContainer()->get(UserRepository::class);

        $user = $repository->findOneBy([
            'email' => 'guest@test.com',
        ]);

        $repository->upgradePassword($user, 'new-hashed-password');

        $this->assertSame('new-hashed-password', $user->getPassword());
    }

    public function testUpgradePasswordThrowsExceptionForUnsupportedUser(): void
    {
        self::bootKernel();

        $repository = static::getContainer()->get(UserRepository::class);

        $unsupportedUser = new class implements PasswordAuthenticatedUserInterface {
            public function getPassword(): ?string
            {
                return null;
            }
        };

        $this->expectException(UnsupportedUserException::class);

        $repository->upgradePassword($unsupportedUser, 'new-password');
    }
}