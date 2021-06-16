<?php


namespace App\Helpers;

use Doctrine\Common\Collections\Collection;

class MediaHelpers
{
    public function hydrateMediaArray(array $entities): array
    {
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