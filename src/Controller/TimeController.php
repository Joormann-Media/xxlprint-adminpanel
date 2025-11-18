<?php

// src/Controller/TimeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TimeController extends AbstractController
{
    /**
     * @Route("/get-time", name="get_time")
     */
    public function getTime(): JsonResponse
    {
        $timezone = new \DateTimeZone('Europe/Berlin');
        $time = new \DateTime('now', $timezone);
        
        // Formatieren des Datums im gewünschten Format "DD. Monat YYYY"
        setlocale(LC_TIME, 'de_DE.UTF-8');
        $formattedDate = $time->format('d. F Y');

        // Formatieren der Uhrzeit
        $formattedTime = $time->format('H:i:s');

        return $this->json(['date' => $formattedDate, 'time' => $formattedTime]);
    }
}