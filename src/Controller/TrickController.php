<?php

namespace App\Controller;

use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/tricks/{id}", name="trick_show")
     */
    public function index(int $id): Response
    {
        $trickRepository = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $trickRepository->findOneBy(['id' => $id]);
        dd($trick);
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }

    /**
     * @Route("/tricks/new", name="trick_new")
     */
    public function newAction(Request $request): Response
    {
        $trick = new Trick();
        $form = $this->createForm(RegistrationFormType::class, $trick);
        $form->handleRequest($request);
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }

    /**
     * @Route("/tricks/{id}/edit", name="trick_edit")
     */
    public function editAction(Request $request, int $id): Response
    {
        $trickRepository = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $trickRepository->findOneBy(['id' => $id]);
        dd($trick);
        $form->handleRequest($request);
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }


    /**
     * @Route("/tricks/{id}/remove", name="trick_remove")
     */
    public function removeAction(Request $request, int $id): Response
    {
        $trickRepository = $this->getDoctrine()->getRepository(Trick::class);
        $trick = $trickRepository->findOneBy(['id' => $id]);
        dd($trick);
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }

}
