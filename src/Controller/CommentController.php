<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Manager\CommentManager;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/api/comments/new", name="api_new_comment")
     */
    public function index(Request $request, CommentManager $commentManager): Response
    {
        $content = $commentManager->getResponseContent($request);
        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment, ['parentId' => $content['pid'], 'trickId' => $content['trickId']]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('trickId')) {
                $trickId = $form->get('trickId')->getData();
            }
            if ($form->has('parentId')) {
                $parentId = $form->get('parentId')->getData();
            }

            $commentManager->saveComment($comment, $this->getUser(), $trickId ?? null, $parentId ?? null);
            return new JsonResponse(['status' => 201, 'data' => 'success', 'user' => $this->getUser()->getUsername()], 201);
        }

        return new JsonResponse(['data' => $this->render('comment/new.html.twig', ['commentForm' => $form->createView()])->getContent()
        ]);
    }
}
