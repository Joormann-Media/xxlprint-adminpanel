<?php

namespace App\Controller;

use App\Entity\VehicleDocument;
use App\Form\VehicleDocumentType;
use App\Repository\VehicleDocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/vehicle-document')]
final class VehicleDocumentController extends AbstractController
{
    #[Route(name: 'app_vehicle_document_index', methods: ['GET'])]
    public function index(Request $request, VehicleDocumentRepository $vehicleDocumentRepository): Response
    {
        $searchTerm = $request->query->get('search');

        if ($searchTerm) {
            $qb = $vehicleDocumentRepository->createQueryBuilder('vd')
                ->leftJoin('vd.vehicleDoctype', 'dt')
                ->where('dt.doctypeName LIKE :term OR vd.vehicleDocimage LIKE :term')
                ->setParameter('term', '%'.$searchTerm.'%');
            $vehicle_documents = $qb->getQuery()->getResult();
        } else {
            $vehicle_documents = $vehicleDocumentRepository->findAll();
        }

        return $this->render('vehicle_document/index.html.twig', [
            'vehicle_documents' => $vehicle_documents,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/new', name: 'app_vehicle_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicleDocument = new VehicleDocument();
        $vehicleDocument->setVehicleDocadd(new \DateTime('now', new \DateTimeZone('Europe/Berlin')));

        $form = $this->createForm(VehicleDocumentType::class, $vehicleDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $docFile */
            $docFile = $form->get('vehicleDocimage')->getData();

            if ($docFile) {
                $uploadDir = $this->getParameter('vehicle_docs_dir');
                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                    $this->addFlash('danger', 'Upload-Verzeichnis kann nicht erstellt werden.');
                    return $this->render('vehicle_document/new.html.twig', [
                        'vehicle_document' => $vehicleDocument,
                        'form' => $form->createView(),
                    ]);
                }
                $newFilename = uniqid('doc_') . '.' . $docFile->guessExtension();

                try {
                    $docFile->move($uploadDir, $newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Fehler beim Hochladen der Datei: ' . $e->getMessage());
                    return $this->render('vehicle_document/new.html.twig', [
                        'vehicle_document' => $vehicleDocument,
                        'form' => $form->createView(),
                    ]);
                }

                $vehicleDocument->setVehicleDocimage('uploads/vehicle_docs/' . $newFilename);
            } else {
                $vehicleDocument->setVehicleDocimage(null);
            }

            $entityManager->persist($vehicleDocument);
            $entityManager->flush();

            $this->addFlash('success', 'Dokument erfolgreich hinzugefügt.');
            return $this->redirectToRoute('app_vehicle_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle_document/new.html.twig', [
            'vehicle_document' => $vehicleDocument,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_vehicle_document_show', methods: ['GET'])]
    public function show(VehicleDocument $vehicleDocument): Response
    {
        return $this->render('vehicle_document/show.html.twig', [
            'vehicle_document' => $vehicleDocument,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_vehicle_document_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VehicleDocument $vehicleDocument, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehicleDocumentType::class, $vehicleDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $docFile */
            $docFile = $form->get('vehicleDocimage')->getData();

            if ($docFile) {
                $uploadDir = $this->getParameter('vehicle_docs_dir');
                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                    $this->addFlash('danger', 'Upload-Verzeichnis kann nicht erstellt werden.');
                    return $this->render('vehicle_document/edit.html.twig', [
                        'vehicle_document' => $vehicleDocument,
                        'form' => $form->createView(),
                    ]);
                }
                $newFilename = uniqid('doc_') . '.' . $docFile->guessExtension();

                try {
                    $docFile->move($uploadDir, $newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Fehler beim Hochladen der Datei: ' . $e->getMessage());
                    return $this->render('vehicle_document/edit.html.twig', [
                        'vehicle_document' => $vehicleDocument,
                        'form' => $form->createView(),
                    ]);
                }

                $vehicleDocument->setVehicleDocimage('uploads/vehicle_docs/' . $newFilename);
            }
            // Wenn kein neues File → altes bleibt erhalten

            $entityManager->flush();

            $this->addFlash('success', 'Dokument aktualisiert.');
            return $this->redirectToRoute('app_vehicle_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle_document/edit.html.twig', [
            'vehicle_document' => $vehicleDocument,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_vehicle_document_delete', methods: ['POST'])]
    public function delete(Request $request, VehicleDocument $vehicleDocument, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete' . $vehicleDocument->getId(), $token)) {
            $vehicleDocument->setVehicleDocadd(new \DateTime('now', new \DateTimeZone('Europe/Berlin')));
            $entityManager->remove($vehicleDocument);
            $entityManager->flush();
            $this->addFlash('success', 'Dokument gelöscht.');
        } else {
            $this->addFlash('danger', 'Ungültiger CSRF-Token – Löschung abgebrochen!');
        }

        return $this->redirectToRoute('app_vehicle_document_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/ajax/list', name: 'app_vehicle_document_ajax_list', methods: ['GET'])]
    public function ajaxList(Request $request, VehicleDocumentRepository $repo): Response
    {
        $search = $request->query->get('search');

        if ($search) {
            $qb = $repo->createQueryBuilder('vd')
                ->leftJoin('vd.vehicleDoctype', 'dt')
                ->where('dt.doctypeName LIKE :term OR vd.vehicleDocimage LIKE :term')
                ->setParameter('term', '%'.$search.'%');
            $vehicle_documents = $qb->getQuery()->getResult();
        } else {
            $vehicle_documents = $repo->findAll();
        }

        $table = $this->renderView('vehicle_document/_table.html.twig', [
            'vehicle_documents' => $vehicle_documents,
        ]);
        $pagination = ''; // Placeholder für Pagination

        return $this->json(['table' => $table, 'pagination' => $pagination]);
    }
}
