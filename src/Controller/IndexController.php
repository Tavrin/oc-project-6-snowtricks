<?php

namespace App\Controller;

use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $trickRepository = $this->getDoctrine()->getRepository(Trick::class);
        $content['tricks'] = $trickRepository->findAll();
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'content' => $content
        ]);
    }
}
