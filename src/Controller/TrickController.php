<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentFormType;
use App\Form\TrickFormType;
use App\Manager\TrickManager;
use App\Repository\MediaRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickController extends AbstractController
{
    /**
     * @Route("/tricks/{slug}", name="trick_show")
     */
    public function index(Request $request, Trick $trick): Response
    {
        $content['trick'] = $trick;
        $content['comments'] = $trick->getCommentsWithoutParent();
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setStatus(true);
            $comment->setCreatedat(new DateTime);
            $comment->setUser($this->getUser());
            $trick->addComment($comment);
            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Commentaire posté');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }
        return $this->render('trick/index.html.twig', [
            'content' => $content,
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/tricks/new", priority="10", name="trick_new")
     */
    public function newAction(Request $request, MediaRepository $mediaRepository, SluggerInterface $slugger): Response
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

        if ($form->isSubmitted() && $form->isValid()) {
            $media = $form->get('mainMedia')->getData();
            if (!empty($media = $mediaRepository->findOneBy(['file' => $media]))) {
                $trick->setMainMedia($media);
            }

            $trick->setCreatedat(new DateTime);
            $trick->setSlug($slugger->slug($trick->getName()));
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Figure créée avec succès');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/editor.html.twig', [
            'editorForm' => $form->createView(),
            'type' => 'new'
        ]);
    }

    /**
     * @Route("/tricks/{slug}/edit", name="trick_edit")
     */
    public function editAction(Request $request, Trick $trick, TrickManager $trickManager): Response
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
            $trickManager->saveTrick($trick, $form);
            $this->addFlash('success', 'Figure modifiée avec succès');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }
        return $this->render('trick/editor.html.twig', [
            'editorForm' => $form->createView(),
            'trick' => $trick,
            'type' => 'edit'
        ]);
    }


    /**
     * @Route("/tricks/{slug}/delete", name="trick_remove")
     */
    public function removeAction(Request $request, Trick $trick): Response
    {
        $this->getDoctrine()->getManager()->remove($trick);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'La figure '.$trick->getName().' a été supprimée avec succès');

        return $this->redirectToRoute('index');
    }

}
