<?php

// src/Controller/ApiAutocompleteController.php
namespace App\Controller;

use App\Repository\EmployeeRepository;
use App\Repository\SchoolkidsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ApiAutocompleteController extends AbstractController
{
    #[Route('/api/autocomplete/employee', name: 'api_employee_autocomplete')]
public function employee(Request $request, EmployeeRepository $repo): JsonResponse
{
    $q = trim($request->query->get('q', ''));
    $results = [];
    if (mb_strlen($q) >= 2) {
        $found = $repo->createQueryBuilder('e')
            ->andWhere('LOWER(e.lastName) LIKE :q OR LOWER(e.firstName) LIKE :q OR e.employeeNumber LIKE :q')
            ->setParameter('q', '%' . mb_strtolower($q) . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
        foreach ($found as $e) {
            $results[] = [
                'id' => $e->getId(),
                'text' => $e->getLastName() . ' ' . $e->getFirstName() . ' (' . $e->getEmployeeNumber() . ')',
            ];
        }
    }
    return new JsonResponse(['results' => $results]);
}


    #[Route('/api/autocomplete/schoolkid', name: 'api_schoolkid_autocomplete')]
public function schoolkid(Request $request, SchoolkidsRepository $repo): JsonResponse
{
    $q = trim($request->query->get('q', ''));
    $results = [];
    if (mb_strlen($q) >= 2) {
        $found = $repo->createQueryBuilder('k')
            ->andWhere('LOWER(k.lastName) LIKE :q OR LOWER(k.firstName) LIKE :q')
            ->setParameter('q', '%' . mb_strtolower($q) . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
        foreach ($found as $k) {
            $results[] = [
                'id' => $k->getId(),
                'text' => $k->getLastName() . ' ' . $k->getFirstName(),
            ];
        }
    }
    return new JsonResponse(['results' => $results]);
}

}

