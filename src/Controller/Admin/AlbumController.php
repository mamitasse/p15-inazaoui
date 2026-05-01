<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Form\AlbumType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/album')] // ✅ Préfixe commun pour toutes les routes
class AlbumController extends AbstractController
{
    // Liste des albums
    #[Route('', name: 'admin_album_index')]
    public function index(EntityManagerInterface $entityManager)
    {
        $albums = $entityManager->getRepository(Album::class)->findAll();

        return $this->render('admin/album/index.html.twig', [
            'albums' => $albums,
        ]);
    }

    // Ajouter un album
    #[Route('/add', name: 'admin_album_add')]
    public function add(Request $request, EntityManagerInterface $entityManager)
    {
        $album = new Album();

        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($album);
            $entityManager->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Modifier un album
    #[Route('/update/{id}', name: 'admin_album_update')]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager)
    {
        $album = $entityManager->getRepository(Album::class)->find($id);

        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Supprimer un album
    #[Route('/admin/album/delete/{id}', name: 'admin_album_delete', methods: ['POST'])]
public function delete(Request $request, Album $album, EntityManagerInterface $entityManager)
{
    // 🔐 Vérification du token CSRF
    if ($this->isCsrfTokenValid('delete'.$album->getId(), $request->request->get('_token'))) {

        $entityManager->remove($album);
        $entityManager->flush();
    }

    return $this->redirectToRoute('admin_album_index');
}
}