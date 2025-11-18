<?php

namespace App\Controller;

use App\Entity\PopUpManager;
use App\Entity\VacationManager;
use App\Entity\HelpEntry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PopUpController extends AbstractController
{
    #[Route('/popup/view/{id<\d+>}', name: 'popup_view', methods: ['GET'])]
    public function viewPopup(PopUpManager $popup): Response
    {
        return new Response($popup->getPopupContent());
    }
    
    #[Route('/vacation/view/{id<\d+>}', name: 'vacation_popup_view', methods: ['GET'])]
    public function viewVacationPopup(VacationManager $vacation): Response
    {
        return new Response($vacation->getVacationContent());
    }

    #[Route('/help/view/{id<\d+>}', name: 'help_popup_view', methods: ['GET'])]
    public function viewHelpPopup(HelpEntry $helpEntry): Response
    {
        return new Response($helpEntry->getContent());
    }

}
