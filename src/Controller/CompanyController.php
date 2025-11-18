<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/company')]
final class CompanyController extends AbstractController
{
    public function __construct()
    {
        // Constructor without MenuService
    }

    #[Route(name: 'app_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository): Response
    {
        return $this->render('company/index.html.twig', [
            'companies' => $companyRepository->findAll(),
            'page_title' => 'Company - Übersicht',
        ]);
    }

    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/new.html.twig', [
            'company' => $company,
            'form' => $form,
            'page_title' => 'Company - Anlegen',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_company_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
        return $this->render('company/show.html.twig', [
            'company' => $company,
            'page_title' => 'Company - Details',
        ]);
    }

    #[Route('/edit/{id<\d+>}', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('companyLogo')->getData();
    
            // 🔥 Falls eine echte Datei hochgeladen wird (normaler Upload)
            if ($file instanceof UploadedFile) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
    
                try {
                    $file->move($this->getParameter('upload_directory'), $newFilename);
                    $company->setCompanyLogo($newFilename); // Nur den Dateinamen speichern!
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Fehler beim Hochladen der Datei.');
                }
            } 
            // 🔥 Falls der Dateiname bereits per AJAX gesetzt wurde
            elseif (is_string($file) && !empty($file)) {
                $company->setCompanyLogo(basename($file)); // Nur den Dateinamen extrahieren!
            }
    
            $em->persist($company);
            $em->flush();
    
            $this->addFlash('success', 'Firmendaten erfolgreich aktualisiert.');
            return $this->redirectToRoute('app_company_edit', ['id' => $company->getId()]);
            
        }
    
        return $this->render('company/edit.html.twig', [
            'form' => $form->createView(),
            'company' => $company,
            'page_title' => 'Company - Bearbeiten',
        ]);
    }

    #[Route('/upload-image', name: 'upload_company_logo', methods: ['POST'])]
    public function uploadImage(Request $request, SluggerInterface $slugger): JsonResponse
    {
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse(['error' => 'Keine Datei hochgeladen'], 400);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getParameter('upload_directory'), $newFilename);
            return new JsonResponse(['location' => '/uploads/' . $newFilename]);
        } catch (FileException $e) {
            return new JsonResponse(['error' => 'Fehler beim Speichern der Datei'], 500);
        }
    }

    #[Route('/{id<\d+>}', name: 'app_company_delete', methods: ['POST'])]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
    }
}
