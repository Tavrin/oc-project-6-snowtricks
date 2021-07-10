<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Manager\CommentManager;
use App\Repository\CommentRepository;
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

    /**
     * @Route("/api/comments", name="comments_api_get")
     */
    public function apiGetComments(Request $request, CommentRepository $commentRepository, CommentManager $commentManager): JsonResponse
    {
        $queries = $request->query->all();
        if (!isset($queries['count']) || !isset($queries['id'])) {
            return new JsonResponse(['status' => 500,'response' => 'Une erreur est survenue']);
        }

        $comments = $commentRepository->findCommentsListing($queries['count']);
        $comments = $commentRepository->trickFilter($comments, $queries['id']);
        $comments = $commentRepository->paginate($comments);
        $content['count'] = count($comments);
        $content['comments'] = $commentManager->hydrateCommentArray($comments->getQuery()->getResult());
        return new JsonResponse(['status' => 200, 'response' => $content], 200);
    }
}
