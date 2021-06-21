<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Manager\CommentManager;
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
            $commentManager->saveComment($comment, $this->getUser(), $form);
            return new JsonResponse(['status' => 201, 'data' => 'success', 'user' => $this->getUser()->getUsername()], 201);
        }

        return new JsonResponse(['data' => $this->render('comment/new.html.twig', ['commentForm' => $form->createView()])->getContent()]);
    }
}
