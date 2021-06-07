<?php


namespace App\Helpers;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaHelpers
{
    private ?string $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

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
            $hydratedArray[$key]['file'] = $this->targetDirectory.'/'.$entity->getFile();
        }

        return $hydratedArray;
    }
}