<?php


namespace App\Helpers;

use App\Entity\Trick;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;

class MediaHelpers
{
    private ManagerRegistry $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function hydrateMediaArray($entities, $queries = null): array
    {
        $hydratedArray = [];
        if (isset($queries['excluded-tricks'])) {
            $tricks = $queries['excluded-tricks'];
            foreach (explode(',', $tricks) as $trick) {
                $trick = $this->doctrine->getRepository(Trick::class)->findOneBy(['slug' => $trick]);
                if (!$trick) {
                    continue;
                }

                foreach ($entities as $media) {
                    if ($trick->hasMedia($media->getId())) {
                        continue;
                    }

                    $hydratedArray[] = $media;
                }

            }
            $entities = $hydratedArray;
        }

        $hydratedArray = [];

        foreach ($entities as $key => $entity)
        {
            $hydratedArray[$key]['id'] = $entity->getId();
            $hydratedArray[$key]['name'] = $entity->getName();
            $hydratedArray[$key]['createdAt'] = $entity->getCreatedAt()->format("Y-m-d\TH:i:s");
            $hydratedArray[$key]['updatedAt'] = $entity->getUpdatedAt() ? $entity->getUpdatedAt()->format("Y-m-d\TH:i:s") : null;
            $hydratedArray[$key]['caption'] = $entity->getCaption();
            $hydratedArray[$key]['file'] = '/uploads/tricks-images/'.$entity->getFile();
        }

        return $hydratedArray;
    }

    public function hydrateVideoArray(Collection $entities): array
    {
        $hydratedArray = [];
        foreach ($entities as $key => $entity)
        {
            $hydratedArray[$key]['id'] = $entity->getId();
            $hydratedArray[$key]['name'] = $entity->getName();
            $hydratedArray[$key]['createdAt'] = $entity->getCreatedAt()->format("Y-m-d\TH:i:s");
            $hydratedArray[$key]['url'] = $entity->getUrl();
            $hydratedArray[$key]['type'] = $entity->getVideoType()->getName();
            $hydratedArray[$key]['updatedAt'] = $entity->getUpdatedAt() ? $entity->getUpdatedAt()->format("Y-m-d\TH:i:s") : null;
        }

        return $hydratedArray;
    }
}