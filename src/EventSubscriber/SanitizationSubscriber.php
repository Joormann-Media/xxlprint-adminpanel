<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use App\Service\SanitizerService;

class SanitizationSubscriber implements EventSubscriber
{
    private SanitizerService $sanitizer;

    public function __construct(SanitizerService $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->sanitizeEntity($args->getEntity());
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->sanitizeEntity($args->getEntity());
    }

    private function sanitizeEntity(object $entity): void
    {
        if (method_exists($entity, 'updateSanitizedFields')) {
            $entity->updateSanitizedFields($this->sanitizer);
        }
    }
}
