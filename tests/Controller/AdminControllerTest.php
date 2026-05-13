<?php

namespace App\Tests\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminControllerTest extends WebTestCase
{
    /**
     * Méthode réutilisable pour connecter l'administrateur.
     */
    private function loginAdmin($client): void
    {
        $client->request('GET', '/login');

        $client->submitForm('Connexion', [
            '_username' => 'ina@test.com',
            '_password' => 'password',
        ]);

        $client->followRedirect();
    }

    public function testAdminCanAccessMediaIndex(): void
    {
        $client = static::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/media');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Medias');
    }

    public function testAdminCanAccessAlbumIndex(): void
    {
        $client = static::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/album');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Albums');
    }

    public function testAdminCanAccessGuestIndex(): void
    {
        $client = static::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/guests');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Liste des invités');
    }

    public function testAdminCanAccessAddAlbumPage(): void
    {
        $client = static::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/album/add');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminCanAccessAddMediaPage(): void
    {
        $client = static::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/media/add');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminCanAccessAddGuestPage(): void
    {
        $client = static::createClient();

        $this->loginAdmin($client);

        $client->request('GET', '/admin/guests/new');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminCanAccessUpdateAlbumPage(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'ina@test.com']);

        $client->loginUser($admin);

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $album = $entityManager->getRepository(Album::class)->findOneBy([]);

        $client->request('GET', '/admin/album/update/' . $album->getId());

        $this->assertResponseIsSuccessful();
    }

    public function testAdminCanToggleGuest(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneBy(['email' => 'ina@test.com']);

        $client->loginUser($admin);

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $guest = $entityManager->getRepository(User::class)->findOneBy([
            'email' => 'guest@test.com',
        ]);

        $client->request('GET', '/admin/guests/toggle/' . $guest->getId());

        $this->assertResponseRedirects('/admin/guests');
    }

    public function testAdminCanDeleteMedia(): void
{
    $client = static::createClient();

    $userRepository = static::getContainer()->get(UserRepository::class);
    $admin = $userRepository->findOneBy(['email' => 'ina@test.com']);

    $client->loginUser($admin);

    $entityManager = static::getContainer()->get(EntityManagerInterface::class);

    // On crée un média spécialement pour le test de suppression.
    $media = new Media();
    $media->setTitle('Media à supprimer');
    $media->setPath('images/test-delete.jpg');
    $media->setUser($admin);

    $entityManager->persist($media);
    $entityManager->flush();

    // On ouvre la page de liste pour récupérer le vrai token CSRF généré par Twig.
    $crawler = $client->request('GET', '/admin/media');

    // On cherche le formulaire de suppression correspondant au média créé.
    $form = $crawler
        ->filter('form[action="/admin/media/admin/media/delete/' . $media->getId() . '"]')
        ->form();

    // On soumet le formulaire avec le token CSRF valide.
    $client->submit($form);

    $this->assertResponseRedirects('/admin/media');
}

    public function testAdminCanCreateAlbum(): void
    {
        $client = static::createClient();

        $this->loginAdmin($client);

        // On ouvre d'abord le formulaire pour récupérer automatiquement le token CSRF.
        $crawler = $client->request('GET', '/admin/album/add');

        $form = $crawler->selectButton('Ajouter')->form([
            'album[name]' => 'Album test ' . uniqid(),
        ]);

        $client->submit($form);

        $this->assertResponseRedirects();
    }

    public function testAdminCanDeleteAlbum(): void
    {
        $client = static::createClient();

        $this->loginAdmin($client);

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $album = new Album();
        $album->setName('Album à supprimer');

        $entityManager->persist($album);
        $entityManager->flush();

        $client->request('POST', '/admin/album/admin/album/delete/' . $album->getId());

        $this->assertResponseRedirects();
    }

    public function testAdminCanCreateGuest(): void
    {
        $client = static::createClient();

        $this->loginAdmin($client);

        // On ouvre d'abord le formulaire pour récupérer automatiquement le token CSRF.
        $crawler = $client->request('GET', '/admin/guests/new');

        $form = $crawler->selectButton('Créer')->form([
            'guest[name]' => 'new-guest',
            'guest[email]' => 'newguest-' . uniqid() . '@test.com',
            'guest[password]' => 'password',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects();
    }

    public function testAdminCanDeleteGuest(): void
    {
        $client = static::createClient();

        $this->loginAdmin($client);

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $guest = new User();
        $guest->setName('guest-delete');
        $guest->setEmail('guestdelete-' . uniqid() . '@test.com');
        $guest->setAdmin(false);
        $guest->setIsActive(true);
        $guest->setRoles(['ROLE_USER']);
        $guest->setPassword('password');

        $entityManager->persist($guest);
        $entityManager->flush();

        $client->request('POST', '/admin/guests/delete/' . $guest->getId(), [
            '_token' => 'test',
        ]);

        $this->assertResponseRedirects();
    }

public function testAdminCanCreateMedia(): void
{
    $client = static::createClient();

    $this->loginAdmin($client);

    $imagePath = tempnam(sys_get_temp_dir(), 'test_image') . '.jpg';

    file_put_contents(
        $imagePath,
        base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2w==')
    );

    $crawler = $client->request('GET', '/admin/media/add');

    $form = $crawler->selectButton('Ajouter')->form();

    $form['media[title]'] = 'Media test upload';
    $form['media[file]']->upload($imagePath);

    $client->submit($form);

    $this->assertResponseRedirects();
}

public function testAdminCanUpdateAlbum(): void
{
    $client = static::createClient();

    $this->loginAdmin($client);

    $entityManager = static::getContainer()->get(EntityManagerInterface::class);

    $album = new Album();
    $album->setName('Album avant modification');

    $entityManager->persist($album);
    $entityManager->flush();

    $crawler = $client->request('GET', '/admin/album/update/' . $album->getId());

    $form = $crawler->selectButton('Modifier')->form([
        'album[name]' => 'Album modifié',
    ]);

    $client->submit($form);

    $this->assertResponseRedirects();
}

}