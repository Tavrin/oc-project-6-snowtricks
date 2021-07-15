<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TrickRepository $trickRepository): Response
    {
        $content['tricks'] = $trickRepository->findAll();
        return $this->render('index/index.html.twig', [
            'content' => $content
        ]);
    }
}
