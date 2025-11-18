<?php

namespace App\Twig;

use App\Repository\MessageStatusRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Extension\GlobalsInterface;

class MessageStatusExtension extends AbstractExtension implements GlobalsInterface
{
    private $messageStatusRepo;
    private $security;

    public function __construct(MessageStatusRepository $messageStatusRepo, Security $security)
    {
        $this->messageStatusRepo = $messageStatusRepo;
        $this->security = $security;
    }

    public function getGlobals(): array
    {
        $user = $this->security->getUser();
        if (!$user) {
            return [
                'unreadMessagesCount' => 0,
                'urgentNewsCount' => 0,
            ];
        }

        return [
            'unreadMessagesCount' => $this->messageStatusRepo->countUnreadForUser($user),
            'urgentNewsCount'     => $this->messageStatusRepo->countUrgentNewsForUser($user),
        ];
    }
}
