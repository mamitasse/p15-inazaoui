<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageIsSuccessful(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    public function testGuestsPageIsSuccessful(): void
    {
        $client = static::createClient();

        $client->request('GET', '/guests');

        $this->assertResponseIsSuccessful();
    }

    public function testPortfolioPageIsSuccessful(): void
    {
        $client = static::createClient();

        $client->request('GET', '/portfolio');

        $this->assertResponseIsSuccessful();
    }

    public function testBlockedMediaIsNotVisible(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/portfolio');

        $this->assertResponseIsSuccessful();

        $this->assertStringNotContainsString(
            'Photo Bloquée',
            $crawler->filter('body')->text()
        );
    }

    public function testGuestCannotAccessAdmin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/guests');

        $this->assertResponseRedirects('/login');
    }

    public function testAdminCanAccessAdminPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $client->submitForm('Connexion', [
            '_username' => 'ina@test.com',
            '_password' => 'password',
        ]);
        $client->followRedirect();

        $client->request('GET', '/admin/guests');

        $this->assertResponseIsSuccessful();
    }
}