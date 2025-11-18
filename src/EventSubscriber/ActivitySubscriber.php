<?php

// src/EventSubscriber/ActivitySubscriber.php

namespace App\EventSubscriber;

use App\Service\OnlineStatusService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Bundle\SecurityBundle\Security;

class ActivitySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private OnlineStatusService $onlineStatusService
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $user = $this->security->getUser();
        if ($user && method_exists($user, 'getId')) {
            $this->onlineStatusService->markOnline($user);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }
}

