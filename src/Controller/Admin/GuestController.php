<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\GuestType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/guests')]
#[IsGranted('ROLE_ADMIN')]
class GuestController extends AbstractController
{
    #[Route('', name: 'admin_guest_index')]
    public function index(UserRepository $userRepository): Response
    {
        // On récupère uniquement les invités (non admin)
        $guests = $userRepository->createQueryBuilder('u')
            ->andWhere('u.admin = false')
            ->getQuery()
            ->getResult();

        return $this->render('admin/guest/index.html.twig', [
            'guests' => $guests,
        ]);
    }

   #[Route('/toggle/{id}', name: 'admin_guest_toggle')]
public function toggle(User $user, EntityManagerInterface $em): Response
{
    // On inverse le statut : actif devient bloqué, bloqué devient actif
    $user->setIsActive(!$user->isActive());

    // On s’assure que Doctrine prend bien en compte la modification
    $em->persist($user);

    // On enregistre la modification en base de données
    $em->flush();

    return $this->redirectToRoute('admin_guest_index');
}


#[Route('/new', name: 'admin_guest_new')]
public function new(
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $passwordHasher
): Response {
    $user = new User();

    $form = $this->createForm(GuestType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // Récupération du mot de passe
        $plainPassword = $form->get('password')->getData();

        // Hash du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);

        // invité = non admin
        $user->setAdmin(false);

        // actif par défaut
        $user->setIsActive(true);

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('admin_guest_index');
    }

    return $this->render('admin/guest/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/delete/{id}', name: 'admin_guest_delete', methods: ['POST'])]
public function delete(User $user, Request $request, EntityManagerInterface $em): Response
{
    // Sécurité : on vérifie que le formulaire vient bien de notre site
    if ($this->isCsrfTokenValid('delete_guest_' . $user->getId(), $request->request->get('_token'))) {

        // On supprime d'abord les médias liés à l'invité
        foreach ($user->getMedias() as $media) {
            $em->remove($media);
        }

        // Puis on supprime l'invité
        $em->remove($user);
        $em->flush();
    }

    return $this->redirectToRoute('admin_guest_index');
}
}