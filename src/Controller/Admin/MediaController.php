<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/media')] // ✅ Préfixe commun
class MediaController extends AbstractController
{
    // Liste des médias
    #[Route('', name: 'admin_media_index')]
public function index(EntityManagerInterface $entityManager)
{
    $medias = $entityManager->getRepository(Media::class)->findAll();

    return $this->render('admin/media/index.html.twig', [
        'medias' => $medias,
        'total' => count($medias),
        'page' => 1,
    ]);
}

    // Ajouter un média
    #[Route('/add', name: 'admin_media_add')]
    public function add(Request $request, EntityManagerInterface $entityManager)
    {
        $media = new Media();

        $form = $this->createForm(MediaType::class, $media, [
            'is_admin' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($media);
            $entityManager->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Supprimer un média
    #[Route('/admin/media/delete/{id}', name: 'admin_media_delete', methods: ['POST'])]
public function delete(Request $request, Media $media, EntityManagerInterface $entityManager)
{
    // Vérification CSRF
    if ($this->isCsrfTokenValid('delete'.$media->getId(), $request->request->get('_token'))) {

        $entityManager->remove($media);
        $entityManager->flush();
    }

    return $this->redirectToRoute('admin_media_index');
}
}