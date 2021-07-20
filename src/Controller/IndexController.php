<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\SearchTricksFormType;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(Request $request, TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->findTricksListing();
        $content['count'] = count($trick);
        $content['tricks'] = $trick->getQuery()->getResult();
        $form = $this->createForm(SearchTricksFormType::class);

        return $this->render('index/index.html.twig', [
            'content' => $content,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/mentions-legales", name="mentions_legales", methods={"GET"})
     */
    public function legalMentionsAction(): Response
    {
        return $this->render('index/legal-mentions.html.twig');
    }
}
