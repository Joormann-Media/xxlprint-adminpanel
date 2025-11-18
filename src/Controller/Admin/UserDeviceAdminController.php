<?php

namespace App\Controller\Admin;

use App\Entity\UserDevice;
use App\Entity\UserDeviceLog;
use App\Repository\UserDeviceRepository;
use App\Repository\UserDeviceLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user-devices')]
class UserDeviceAdminController extends AbstractController
{
    #[Route('/', name: 'admin_user_device_index')]
    public function index(
        UserDeviceRepository $deviceRepo,
        UserDeviceLogRepository $logRepo
    ): Response {
        $devices = $deviceRepo->findAll();
        $logs = $logRepo->findBy([], ['timestamp' => 'DESC'], 100);

        return $this->render('admin/user_device/index.html.twig', [
            'devices' => $devices,
            'logs' => $logs
        ]);
    }
}
