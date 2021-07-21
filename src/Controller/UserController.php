<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModifyProfileFormType;
use App\Form\UserChangesFormType;
use App\Repository\CommentRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    private string $userDirectory;

    public function __construct(string $userDirectory)
    {
        $this->userDirectory = $userDirectory;
    }

    /**
     * @Route("/settings", name="user")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $form = $this->createForm(UserChangesFormType::class, $user);

        return $this->render('user/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/settings/profile", name="user_profile_settings")
     */
    public function profileSettingsAction(Request $request, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $folder = $this->userDirectory;

        $user = $this->getUser();
        $form = $this->createForm(ModifyProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file) {
                $newFilename = $fileUploader->upload($file, $folder);
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
        $content['comments'] = $commentRepository->findCommentsListing(0, false, 10);
        $content['comments'] = $commentRepository->userFilter($content['comments'], $user->getId());
        $content['comments'] = $commentRepository->paginate($content['comments']);
        return $this->render('user/profile.html.twig', [
            'content' => $content
        ]);
    }

    /**
     * @Route("/users/{slug}/delete", name="delete_user", methods={"GET"})
     */
    public function deleteAction(User $user, TokenStorageInterface $token, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($user->getId() !== $this->getUser()->getId()) {
            $this->addFlash('danger', 'Mauvais utilisateur');
            return $this->redirectToRoute('index');
        }

        $folder = $this->userDirectory;
        if (!empty($user->getPicture())) {
            unlink($folder.'/'.$user->getPicture());
        }

        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();
        $token->setToken();
        $session->invalidate();
        $this->addFlash('success', 'Votre compte a bien été supprimé');
        return $this->redirectToRoute('index');
    }
}
