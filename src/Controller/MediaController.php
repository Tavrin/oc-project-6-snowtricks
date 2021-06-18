<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\TrickMedia;
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
     * @Route("/api/media/images", name="api_media")
     */
    public function apiIndex(Request $request,MediaRepository $mediaRepository, MediaHelpers $helper): JsonResponse
    {
        $queries = $request->query->all();
        $medias = $mediaRepository->findAll();
        $medias = $helper->hydrateMediaArray($medias, $queries);
        return new JsonResponse(['response' => $medias]);
    }

    /**
     * @Route("/api/media/images/new", name="api_new_image", methods={"POST", "GET"})
     */
    public function newImageApi(Request $request, FileUploader $fileUploader): JsonResponse
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
            return new JsonResponse(['status' => 201,'response' => 'Image ajoutée avec succès']);

        }

        if ($form->isSubmitted()) {
            return new JsonResponse(['status' => 500,'response' => 'Une erreur est survenue']);
        }

        return new JsonResponse(['status' => 200,'response' => $this->render('media/new.html.twig', ['form' => $form->createView(), 'type' => 'new'])->getContent()]);
    }


    /**
     * @Route("/api/tricks/{slug}/videos", name="videos_index")
     */
    public function apiVideoIndex(Trick $trick, MediaHelpers $helper): JsonResponse
    {
        $videos = $trick->getVideos();
        $videos = $helper->hydrateVideoArray($videos);
        return new JsonResponse(['response' => $videos]);
    }

    /**
     * @Route("/api/tricks/{slug}/images", name="trick_images", methods={"GET"})
     */
    public function apiTrickImages(Trick $trick,  MediaHelpers $helper): JsonResponse
    {
        $medias = [];
        foreach ($trick->getTrickMedias() as $media) {
            $medias[] = $media->getMedia();
        }
        $medias = $helper->hydrateMediaArray($medias);
        return new JsonResponse(['response' => $medias]);
    }


    /**
     * @Route("/api/tricks/{slug}/images", name="trick_new_image", methods={"POST", "DELETE"})
     */
    public function apiTrickNewImage(Request $request, Trick $trick): JsonResponse
    {
        if (!empty($request->getContent())) {
            $content = $request->toArray();
        } else {
            return new JsonResponse(['status' => 500,'response' => 'no image id was provided'], 500);
        }

        $media = $this->getDoctrine()->getRepository(Media::class)->find((int)$content['id']);
        if (!$media) {
            return new JsonResponse(['status' => 404, 'response' => 'the image doesn\'t exist'], 404);
        }

        if ($request->isMethod('POST')) {
            if (false === $trick->hasMedia((int)$content['id'])) {
                $trickMedia = new TrickMedia();
                $trickMedia->setCreatedat(new DateTime);
                $trickMedia->setTrick($trick);
                $trickMedia->setMedia($media);

                $this->getDoctrine()->getManager()->persist($trickMedia);
                $this->getDoctrine()->getManager()->flush();

                return new JsonResponse(['status' => 201, 'response' => 'image added to trick'], 201);
            }
        } elseif ($request->isMethod('DELETE')) {
            $trick->removeTrickMediaFromMediaId((int)$content['id']);
            $this->getDoctrine()->getManager()->persist($trick);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(['status' => 200, 'response' => 'image removed from trick'], 201);

        }

        return new JsonResponse(['status' => 500, 'response' => "L'image a déjà été ajoutée à la figure"], 500);
    }
}
