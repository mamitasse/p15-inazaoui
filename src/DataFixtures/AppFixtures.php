<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        /*
        |--------------------------------------------------------------------------
        | ADMIN : INA
        |--------------------------------------------------------------------------
        */

        $admin = new User();

        $admin->setName('ina');
        $admin->setEmail('ina@test.com');
        $admin->setAdmin(true);
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);

        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'password')
        );

        $manager->persist($admin);

        /*
        |--------------------------------------------------------------------------
        | INVITÉ ACTIF
        |--------------------------------------------------------------------------
        */

        $guestActive = new User();

        $guestActive->setName('guest-actif');
        $guestActive->setEmail('guest@test.com');
        $guestActive->setAdmin(false);
        $guestActive->setRoles(['ROLE_USER']);
        $guestActive->setIsActive(true);

        $guestActive->setPassword(
            $this->passwordHasher->hashPassword($guestActive, 'password')
        );

        $manager->persist($guestActive);

        /*
        |--------------------------------------------------------------------------
        | INVITÉ BLOQUÉ
        |--------------------------------------------------------------------------
        */

        $guestBlocked = new User();

        $guestBlocked->setName('guest-bloque');
        $guestBlocked->setEmail('blocked@test.com');
        $guestBlocked->setAdmin(false);
        $guestBlocked->setRoles(['ROLE_USER']);
        $guestBlocked->setIsActive(false);

        $guestBlocked->setPassword(
            $this->passwordHasher->hashPassword($guestBlocked, 'password')
        );

        $manager->persist($guestBlocked);

        /*
        |--------------------------------------------------------------------------
        | ALBUM
        |--------------------------------------------------------------------------
        */

        $album = new Album();

        $album->setName('Voyages');

        $manager->persist($album);

        /*
        |--------------------------------------------------------------------------
        | MEDIA ADMIN
        |--------------------------------------------------------------------------
        */

        $mediaAdmin = new Media();

        $mediaAdmin->setTitle('Photo Ina');
        $mediaAdmin->setPath('images/test-admin.jpg');
        $mediaAdmin->setUser($admin);
        $mediaAdmin->setAlbum($album);

        $manager->persist($mediaAdmin);

        /*
        |--------------------------------------------------------------------------
        | MEDIA INVITÉ ACTIF
        |--------------------------------------------------------------------------
        */

        $mediaGuest = new Media();

        $mediaGuest->setTitle('Photo Guest');
        $mediaGuest->setPath('images/test-guest.jpg');
        $mediaGuest->setUser($guestActive);
        $mediaGuest->setAlbum($album);

        $manager->persist($mediaGuest);

        /*
        |--------------------------------------------------------------------------
        | MEDIA INVITÉ BLOQUÉ
        |--------------------------------------------------------------------------
        */

        $mediaBlocked = new Media();

        $mediaBlocked->setTitle('Photo Bloquée');
        $mediaBlocked->setPath('images/test-blocked.jpg');
        $mediaBlocked->setUser($guestBlocked);
        $mediaBlocked->setAlbum($album);

        $manager->persist($mediaBlocked);

        $manager->flush();
    }
}