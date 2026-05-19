<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    // Page d'accueil du site.
    #[Route('/', name: 'home')]
    public function home(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('front/home.html.twig');
    }

// Page publique qui liste les invités actifs avec pagination.
#[Route('/guests', name: 'guests')]
public function guests(
    Request $request,
    EntityManagerInterface $entityManager
): \Symfony\Component\HttpFoundation\Response {
    // Numéro de page demandé dans l’URL.
    // Exemple : /guests?page=2
    $page = max(1, $request->query->getInt('page', 1));

    // Nombre d’invités affichés par page.
    $limit = 12;

    // Requête optimisée :
    
    // - uniquement les invités non admin ;
    // - uniquement les invités actifs ;
    // - tri alphabétique ;
    // - pagination pour éviter de charger toute la table.
    $queryBuilder = $entityManager
        ->getRepository(User::class)
        ->createQueryBuilder('u')
        ->andWhere('u.admin = false')
        ->andWhere('u.isActive = true')
        ->orderBy('u.name', 'ASC')
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);

    $guests = $queryBuilder
        ->getQuery()
        ->getResult();

    // Requête légère pour connaître le nombre total d’invités actifs.
    $totalGuests = $entityManager
        ->getRepository(User::class)
        ->createQueryBuilder('u')
        ->select('COUNT(u.id)')
        ->andWhere('u.admin = false')
        ->andWhere('u.isActive = true')
        ->getQuery()
        ->getSingleScalarResult();

    return $this->render('front/guests.html.twig', [
        'guests' => $guests,
        'page' => $page,
        'limit' => $limit,
        'totalGuests' => $totalGuests,
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
// Elle affiche uniquement les médias publics autorisés :
// - médias d'Ina
// - médias des invités actifs
// - jamais les médias des invités bloqués
#[Route('/portfolio/{id?}', name: 'portfolio')]
public function portfolio(?int $id, EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
{
    $albums = $entityManager
        ->getRepository(Album::class)
        ->findAll();

    $album = $id
        ? $entityManager->getRepository(Album::class)->find($id)
        : null;

    $queryBuilder = $entityManager
        ->getRepository(Media::class)
        ->createQueryBuilder('m')
        ->leftJoin('m.user', 'u')
        ->addSelect('u')
        ->andWhere('u.admin = true OR u.isActive = true');

    if ($album) {
        $queryBuilder
            ->andWhere('m.album = :album')
            ->setParameter('album', $album);
    }

    $medias = $queryBuilder
        ->getQuery()
        ->getResult();

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