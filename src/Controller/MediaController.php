<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\TrickMedia;
use App\Entity\Video;
use App\Form\MediaFormType;
use App\Form\VideoFormType;
use App\Helpers\MediaHelpers;
use App\Manager\TrickManager;
use App\Repository\MediaRepository;
use App\Service\FileUploader;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/api/tricks/{slug}/videos/new", name="api_new_video", methods={"POST", "GET"})
     */
    public function newVideoApi(Request $request, Trick $trick, TrickManager $trickManager): JsonResponse
    {
        $video = new Video();
        $form = $this->createForm(VideoFormType::class, $video);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickManager->addVideo($video, $trick);

            return new JsonResponse(['status' => 201,'response' => 'Vidéo ajoutée avec succès']);

        }

        if ($form->isSubmitted()) {
            return new JsonResponse(['status' => 500,'response' => 'Une erreur est survenue']);
        }

        return new JsonResponse(['status' => 200,'response' => $this->render('video/new.html.twig', ['form' => $form->createView(), 'video' => $video])->getContent()]);
    }

    /**
     * @Route("/api/videos/{id}", name="video_delete", methods={"DELETE"})
     */
    public function deleteVideoAction(Video $video): JsonResponse
    {
        $this->getDoctrine()->getManager()->remove($video);
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse(['status' => 200, 'response' => 'image deleted'], 201);
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
    public function apiTrickNewImage(Request $request, Trick $trick, MediaHelpers $mediaHelper): JsonResponse
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

        $result = $mediaHelper->manageTrickMedia($request,$content, $media, $trick);

        if (null !== $result) {
            return $result;
        }

        return new JsonResponse(['status' => 500, 'response' => "L'image a déjà été ajoutée à la figure"], 500);
    }

    /**
     * @Route("/api/media/images/{id}", name="delete_image", methods={"DELETE"})
     */
    public function deleteAction(Media $image): JsonResponse
    {
        $this->getDoctrine()->getManager()->remove($image);
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse(['status' => 200, 'response' => 'image deleted'], 201);
    }
}
