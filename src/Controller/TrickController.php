<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\TrickFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/tricks/{id}", name="trick_show")
     */
    public function index(Trick $trick): Response
    {
        $content['trick'] = $trick;
        $content['comments'] = $trick->getComments();
        return $this->render('trick/index.html.twig', [
            'content' => $content
        ]);
    }

    /**
     * @Route("/tricks/new", priority="10", name="trick_new")
     */
    public function newAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (false === $this->getUser()->isVerified()) {
            $this->addFlash(
                'danger',
                "L'adresse email doit être confirmée avant de pouvoir accéder à cette section"
            );
        }

        $trick = new Trick();
        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);
        return $this->render('trick/editor.html.twig', [
            'editorForm' => $form->createView(),
            'type' => 'new'
        ]);
    }

    /**
     * @Route("/tricks/{id}/edit", name="trick_edit")
     */
    public function editAction(Request $request, Trick $trick): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (false === $this->getUser()->isVerified()) {
            $this->addFlash(
                'danger',
                "L'adresse email doit être confirmée avant de pouvoir accéder à cette section"
            );

            return $this->redirectToRoute('user');
        }

        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Figure modifiée avec succès');
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }
        return $this->render('trick/editor.html.twig', [
            'editorForm' => $form->createView(),
            'type' => 'edit'
        ]);
    }


    /**
     * @Route("/tricks/{id}/remove", name="trick_remove")
     */
    public function removeAction(Request $request, Trick $trick): Response
    {
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }

}
