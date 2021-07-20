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

    public function setTrickMainMedia(Trick $trick, FormInterface $form)
    {
        $media = str_replace('/uploads/tricks-images/', '', $form->get('mainMedia')->getData());
        if (!empty($media = $this->em->getRepository(Media::class)->findOneBy(['file' => $media]))) {
            $trick->setMainMedia($media);
        }

        return $trick;
    }

    public function saveTrick(Trick $trick): bool
    {
        $trick->setUpdatedAt(new DateTime);
        $trick->setSlug($this->slugger->slug($trick->getName()));
        $this->em->flush();

        return true;
    }

    public function createTrickAudit(Trick $trick, $user, bool $isNewTrick = false, $oldTrick = null)
    {
        $trickModify = new TrickModify();
        $trickModify->setTrick($trick);
        $trickModify->setUser($user->getSlug());
        if (true === $isNewTrick) {
            $trickModify->setType('new');
        } else {
            $modifies = null;
            if ($trick->getName() !== $oldTrick->getName()) {
                $modifies[] = 'nom de la figure';
            }
            if ($trick->getDescription() !== $oldTrick->getDescription()) {
                $modifies[] = 'description';
            }
            if ($trick->getTrickGroup() !== $oldTrick->getTrickGroup()) {
                $modifies[] = 'groupe de la figure';
            }

            if ($trick->getMainMedia() !== $oldTrick->getMainMedia()) {
                $modifies[] = 'média principal';
            }

            $trickModify->setType('edit');
            $trickModify->setModifiedFields($modifies);
        }
        $this->em->persist($trickModify);
        $this->em->flush();
    }

    public function addVideo(Video $video, Trick $trick)
    {
        $video->setCreatedat(new DateTime);
        $video->setTrick($trick);

        if ('youtube' === $video->getVideoType()->getName()) {
            parse_str( parse_url( $video->getUrl(), PHP_URL_QUERY ), $link );
            $video->setUrl($link['v']);
        }

        $this->em->persist($video);
        $this->em->flush();
    }

    public function getTricks(array $queries, TrickRepository $trickRepository, $limit = 8): Paginator
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