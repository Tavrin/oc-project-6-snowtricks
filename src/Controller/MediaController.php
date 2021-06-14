<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\MediaFormType;
use App\Helpers\MediaHelpers;
use App\Repository\MediaRepository;
use App\Service\FileUploader;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaController extends AbstractController
{
    /**
     * @Route("/api/media", name="api_media")
     */
    public function apiIndex(MediaRepository $mediaRepository, MediaHelpers $helper): JsonResponse
    {
        $medias = $mediaRepository->findAll();
        $medias = $helper->hydrateMediaArray($medias);
        return new JsonResponse(['response' => $medias]);
    }

    /**
     * @Route("/modal/media/new", name="new_media_modal")
     */
    public function newMediaModal(Request $request, MediaRepository $mediaRepository, FileUploader $fileUploader): Response
    {
        $media = new Media();
        $form = $this->createForm(MediaFormType::class, $media, ['required' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $media->setCreatedat(new DateTime);
            if ($file) {
                $newFilename = $fileUploader->upload($file);
                $media->setFile($newFilename);
            }

            $this->getDoctrine()->getManager()->persist($media);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "image ajoutée avec succès");
            $this->redirectToRoute('new_media_modal');
        }

        return $this->render('media/editor.html.twig', [
            'form' => $form->createView(),
            'type' => 'new'
        ]);
    }
}
