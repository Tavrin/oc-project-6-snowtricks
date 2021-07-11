<?php


namespace App\Manager;


use App\Entity\Group;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\TrickModify;
use App\Entity\Video;
use App\Repository\TrickRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
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

    public function createTrickAudit(Trick $trick, $user)
    {
        $trickModify = new TrickModify();
        $trickModify->setTrick($trick);
        $trickModify->setUser($user);
        $this->em->persist($trickModify);
        $this->em->flush();
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

    public function getTricks(array $queries, TrickRepository $trickRepository, $limit = 5): Paginator
    {
        $trickName = null;
        $groupId = null;

        if (isset($queries['trickName']) && !empty($queries['trickName'])) {
            $trickName = $queries['trickName'];
        }
        if (isset($queries['groupId']) && !empty($queries['groupId'])) {
            $groupId = $queries['groupId'];
        }

        if(isset($queries['limit'])) {
            $limit = $queries['limit'];
        }

        return $trickRepository->findTricksListing($queries['count'], $limit, $trickName, $groupId);
    }

    public function setGroups(): array
    {
        return $this->em->getRepository(Group::class)->findAll();
    }

    public function hydrateTrickArray($entities): array
    {
        $content = [];
        $assetManager = new Package(new EmptyVersionStrategy());

        foreach ($entities as $key => $entity) {
            $media = $entity->getMainMedia() ? $entity->getMainMedia()->getFile() : null;
            if (null !== $media) {
                $media = $assetManager->getUrl('uploads/tricks-images/'.$media);
            } else {
                $media = $assetManager->getUrl('build/images/snowboard-1.5194caca.jpg');
            }
            $content[$key]['id'] = $entity->getId();
            $content[$key]['name'] = $entity->getName();
            $content[$key]['slug'] = $entity->getSlug()? $entity->getSlug() : null;
            $content[$key]['createdAt'] = $entity->getCreatedAt()->format("Y-m-d\TH:i:s");
            $content[$key]['updatedAt'] = $entity->getUpdatedAt() ? $entity->getUpdatedAt()->format("Y-m-d\TH:i:s") : null;
            $content[$key]['mainMedia'] = $media;
        }

        return $content;
    }
}