<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifyProfileFormType;
use App\Repository\CommentRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('user/index.html.twig');
    }

    /**
     * @Route("/settings/profile", name="user_profile_settings")
     */
    public function profileSettingsAction(Request $request, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $form = $this->createForm(ModifyProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file) {
                $newFilename = $fileUploader->upload($file);
                $user->setPicture($newFilename);
            }

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('user/profile-settings.html.twig', [
            'form' => $form->createView()
        ]);
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
