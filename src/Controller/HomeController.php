<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    // Page d'accueil du site.
    #[Route('/', name: 'home')]
    public function home(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('front/home.html.twig');
    }

    // Page publique qui liste les invités.
    #[Route('/guests', name: 'guests')]
    public function guests(EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {
        $guests = $entityManager
            ->getRepository(User::class)
            ->findBy(['admin' => false]);

        return $this->render('front/guests.html.twig', [
            'guests' => $guests,
        ]);
    }

    // Page publique d'un invité précis.
    #[Route('/guest/{id}', name: 'guest')]
    public function guest(int $id, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {
        $guest = $entityManager
            ->getRepository(User::class)
            ->find($id);

        return $this->render('front/guest.html.twig', [
            'guest' => $guest,
        ]);
    }

    // Page portfolio.
    // Elle affiche les médias d'Ina, ou les médias d'un album si un id est fourni.
    #[Route('/portfolio/{id?}', name: 'portfolio')]
    public function portfolio(?int $id, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {
        $albums = $entityManager
            ->getRepository(Album::class)
            ->findAll();

        $album = $id
            ? $entityManager->getRepository(Album::class)->find($id)
            : null;

        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['admin' => true]);

        $medias = $album
            ? $entityManager->getRepository(Media::class)->findBy(['album' => $album])
            : $entityManager->getRepository(Media::class)->findBy(['user' => $user]);

        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias,
        ]);
    }

    // Page "Qui suis-je ?"
    #[Route('/about', name: 'about')]
    public function about(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('front/about.html.twig');
    }
}