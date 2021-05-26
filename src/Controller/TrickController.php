<?php

namespace App\Controller;

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
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }

    /**
     * @Route("/tricks/new", priority="10", name="trick_new")
     */
    public function newAction(Request $request): Response
    {
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
        $this->denyAccessUnlessGranted('ROLE_USER');

        if (false === $this->getUser()->isVerified()) {
            dd($this->getUser()->isVerified());
        }

        $form = $this->createForm(TrickFormType::class, $trick);
        $form->handleRequest($request);
        return $this->render('trick/editor.html.twig', [
            'editorForm' => $form->createView(),
            'type' => 'edit'
        ]);
    }


    /**
     * @Route("/tricks/{id}/remove", name="trick_remove")
     */
    public function removeAction(Request $request, int $id): Response
    {
        $trickRepository = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $trickRepository->findOneBy(['id' => $id]);
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }

}
