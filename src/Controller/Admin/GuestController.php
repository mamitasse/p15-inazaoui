<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

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
        // On inverse le statut
        $user->setIsActive(!$user->isActive());

        $em->flush();

        return $this->redirectToRoute('admin_guest_index');
    }
}