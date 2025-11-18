<?php

namespace App\EventListener;

use App\Entity\ReleaseFile;
use App\Service\ReleaseFileUploader;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(event: 'preRemove', priority: 0)]
class DeleteReleaseFileListener
{
    public function __construct(private readonly ReleaseFileUploader $uploader) {}

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ReleaseFile) {
            return;
        }

        $release = $entity->getRelease();
        $filename = $entity->getStoredFilename();

        if (!$filename || !$release) {
            return;
        }

        $path = $this->uploader->getReleasePath($release) . '/' . $filename;

        if (file_exists($path)) {
            @unlink($path);
        }
    }
}


