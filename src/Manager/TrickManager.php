<?php


namespace App\Manager;


use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\Video;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickManager
{
    private ?EntityManagerInterface $em;
    private SluggerInterface $slugger;

    public function __construct(EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $this->em = $em;
        $this->slugger = $slugger;
    }

    public function saveTrick(Trick $trick, FormInterface $form): bool
    {
        $media = str_replace('/uploads/tricks-images/', '', $form->get('mainMedia')->getData());
        if (!empty($media = $this->em->getRepository(Media::class)->findOneBy(['file' => $media]))) {
            $trick->setMainMedia($media);
        }

        $trick->setUpdatedAt(new DateTime);
        $trick->setSlug($this->slugger->slug($trick->getName()));
        $this->em->flush();

        return true;
    }

    public function addVideo(Video $video, Trick $trick)
    {
        $video->setCreatedat(new DateTime);
        $video->setTrick($trick);

        if ('youtube' === $video->getVideoType()->getName()) {
            preg_match('#(\?v=|/v/)([-a-zA-Z0-9]+)#', $video->getUrl(), $link);
            $video->setUrl($link[2]);
        }

        $this->em->persist($video);
        $this->em->flush();
    }
}