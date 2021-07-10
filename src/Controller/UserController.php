<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/settings", name="user")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('user/index.html.twig');
    }

    /**
     * @Route("/users/{slug}", name="user_profile")
     */
    public function profileAction(User $user, CommentRepository $commentRepository): Response
    {
        $content['user'] = $user;
        $content['comments'] = $commentRepository->findCommentsListing(0, false);
        $content['comments'] = $commentRepository->userFilter($content['comments'], $user->getId());
        $content['comments'] = $commentRepository->paginate($content['comments']);
        return $this->render('user/profile.html.twig', [
            'content' => $content
        ]);
    }
}
