<?php

namespace App\Controller\Api;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/company', name: 'api_company')]
final class CompanyApiController extends AbstractController
{
    #[Route('/', name: 'get_company_data', methods: ['GET'])]
    public function getCompanyData(CompanyRepository $companyRepository): JsonResponse
    {
        // Die erste Firma aus der Datenbank holen (falls mehrere existieren, ggf. anpassen)
        $company = $companyRepository->findOneBy([]);

        if (!$company) {
            return new JsonResponse(['error' => 'Keine Firmendaten gefunden'], 404);
        }

        // Funktion zum Setzen von "FOLGT" bei NULL oder leeren Strings
        $sanitize = fn($value) => ($value === null || $value === '') ? 'FOLGT' : $value;

        // JSON-Daten vorbereiten
        $data = [
            'id'            => $company->getId(),
            'company_name'  => $sanitize($company->getCompanyname()),
            'ceo_name'      => $sanitize($company->getCeoname()),
            'ceo_prename'   => $sanitize($company->getCeoprename()),
            'address'       => $sanitize($company->getStreet() . ' ' . $company->getStreetno()),
            'zipcode'       => $sanitize($company->getZipcode()),
            'city'          => $sanitize($company->getCity()),
            'location'      => $sanitize($company->getLocation()),
            'phone'         => $sanitize($company->getPhone()),
            'fax'           => $sanitize($company->getFax()),
            'email'         => $sanitize($company->getEmail()),
            'website'       => $sanitize($company->getWeb()),
            'tax_number'    => $sanitize($company->getTaxno()),
            'vat_id'        => $sanitize($company->getTaxid()),
            'company_logo'  => $sanitize(
                $company->getCompanyLogo() ? 'https://admin.xxl-print-wesel.de/uploads/' . $company->getCompanyLogo() : null
            ),
            'image_path'    => $sanitize(
                $company->getImagePath() ? 'https://admin.xxl-print-wesel.de/uploads/' . $company->getImagePath() : null
            ),
        ];

        return new JsonResponse($data, 200);
    }
}
