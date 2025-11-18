<?php

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use App\Service\OnlineStatusService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigGlobalsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private UserRepository $userRepo,
        private OnlineStatusService $onlineStatus
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        $ids = $this->onlineStatus->getAllOnlineUserIds();
        $users = $this->userRepo->findBy(['id' => $ids]);

        $this->twig->addGlobal('usersOnline', $users);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}

