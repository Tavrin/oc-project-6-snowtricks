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
    public function index(Request $request, CommentRepository $commentRepository, CommentManager $commentManager, TrickRepository $trickRepository): Response
    {
        $content = null;
        if (!empty($request->getContent())) {
            $content = $request->toArray();
        }
        $comment = new Comment();
        $parentId = null;
        if(isset($content['pid'])) {
            if ($commentRepository->find($content['pid'])) {
                $parentId = (int)$content['pid'];
            }
        }

        $trickId = null;
        if (isset($content['trickId'])) {
            if ($trickRepository->find($content['trickId'])) {
                $trickId = (int)$content['trickId'];
            }
        }
        $form = $this->createForm(CommentFormType::class, $comment, ['parentId' => $parentId, 'trickId' => $trickId]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('trickId')) {
                $trickId = $form->get('trickId')->getData();
            }
            if ($form->has('parentId')) {
                $parentId = $form->get('parentId')->getData();
            }
            try {
                $commentManager->saveComment($comment, $this->getUser(), $trickId, $parentId);
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) {
                return new JsonResponse(['data' => 'error'], 500);
            }
            $this->addFlash('success', 'Commentaire posté');
            return new JsonResponse(['data' => 'success', 201]);
        }

        return new JsonResponse(['data' => $this->render('comment/new.html.twig', ['commentForm' => $form->createView()])->getContent()
        ]);
    }
}
