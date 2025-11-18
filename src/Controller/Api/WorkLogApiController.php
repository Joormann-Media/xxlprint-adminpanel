<?php

// src/Controller/Api/WorkLogApiController.php
namespace App\Controller\Api;

use App\Dto\WorkLogClockInRequest;
use App\Entity\WorkLog;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/work-log')]
class WorkLogApiController extends AbstractController
{
    #[Route('/clock-in', name: 'api_work_log_clock_in', methods: ['POST'])]
    public function clockIn(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EmployeeRepository $employeeRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $dto = $serializer->deserialize($request->getContent(), WorkLogClockInRequest::class, 'json');

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $employee = $employeeRepository->findOneBy(['employeeNumber' => $dto->employeeNumber]);

        if (!$employee) {
            return $this->json(['error' => 'Employee not found'], Response::HTTP_NOT_FOUND);
        }

        $workLog = new WorkLog();
        $workLog->setEmployee($employee);
        $workLog->setClockInAt(new \DateTime());
        $workLog->setLocation($dto->location);
        $workLog->setMethod($dto->method);
        $workLog->setDeviceUid($dto->deviceUid);
        $workLog->setSource($dto->source);

        $em->persist($workLog);
        $em->flush();

        return $this->json(['status' => 'clocked_in', 'id' => $workLog->getId()]);
    }
}

