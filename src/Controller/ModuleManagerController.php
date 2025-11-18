<?php

namespace App\Controller;

use App\Entity\ModuleManager;
use App\Form\ModuleManagerForm;
use App\Repository\ModuleManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/module-manager')]
final class ModuleManagerController extends AbstractController
{
    // ---------- UUIDv4 Generator mit Präfixen ----------
    private function generateUuidV4(string $prefix = ''): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10
        return $prefix . vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    #[Route(name: 'app_module_manager_index', methods: ['GET'])]
    public function index(ModuleManagerRepository $moduleManagerRepository): Response
    {
        return $this->render('module_manager/index.html.twig', [
            'module_managers' => $moduleManagerRepository->findAll(),
            'page_title' => 'Module Manager',
            'page_description' => 'Manage your modules and their configurations.',
        ]);
    }

    #[Route('/new', name: 'app_module_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $moduleManager = new ModuleManager();

        // Timestamps bei Anlage
        if ($moduleManager->getCreate() === null) {
            $moduleManager->setCreate(new \DateTimeImmutable());
        }
        if ($moduleManager->getLastUpdate() === null) {
            $moduleManager->setLastUpdate(new \DateTimeImmutable());
        }

        // IDs auto-generieren, wenn leer
        if (empty($moduleManager->getLogId())) {
            $moduleManager->setLogId($this->generateUuidV4('log_'));
        }
        if (empty($moduleManager->getModuleID())) {
            $moduleManager->setModuleID($this->generateUuidV4('mod_'));
        }

        $form = $this->createForm(ModuleManagerForm::class, $moduleManager, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $moduleManager->setCreate(new \DateTime());
            $moduleManager->setLastUpdate(new \DateTime());

            // Files vom Hidden-Feld holen
            $filesRaw = $form->get('correspondingFiles')->getData();
            $filesArr = [];
            if (is_string($filesRaw) && strlen($filesRaw) > 0) {
                $filesArr = json_decode($filesRaw, true) ?: [];
            }
            $moduleManager->setCorrespondingFiles($filesArr);

            // Falls User die IDs leer gemacht hat, NOCHMAL generieren
            if (empty($moduleManager->getLogId())) {
                $moduleManager->setLogId($this->generateUuidV4('log_'));
            }
            if (empty($moduleManager->getModuleID())) {
                $moduleManager->setModuleID($this->generateUuidV4('mod_'));
            }

            $entityManager->persist($moduleManager);
            $entityManager->flush();

            return $this->redirectToRoute('app_module_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('module_manager/new.html.twig', [
            'module_manager' => $moduleManager,
            'form' => $form,
            'page_title' => 'Create New Module Manager',
            'page_description' => 'Configure a new module with its settings and files.',

        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_module_manager_show', methods: ['GET'])]
    public function show(ModuleManager $moduleManager): Response
    {
        return $this->render('module_manager/show.html.twig', [
            'module_manager' => $moduleManager,
            'page_title' => 'Module Manager Details',
            'page_description' => 'View details of the selected module manager.',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_module_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ModuleManager $moduleManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ModuleManagerForm::class, $moduleManager, [
    'is_edit' => true,
]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Files & MappedEntities decode (wie gehabt)
            $data = $form->get('correspondingFiles')->getData();
            if (is_string($data)) {
                $decoded = json_decode($data, true);
                $moduleManager->setCorrespondingFiles($decoded ?: []);
            }

            $mapped = $form->get('mappedEntitys')->getData();
            if (is_string($mapped)) {
                $decoded = json_decode($mapped, true);
                $moduleManager->setMappedEntitys($decoded ?: []);
            } elseif ($mapped === '') {
                $moduleManager->setMappedEntitys(null); // Handle empty string as null
            }

            // IDs nachziehen falls leer (Benutzer kann sie ja leeren beim Edit)
            if (empty($moduleManager->getLogId())) {
                $moduleManager->setLogId($this->generateUuidV4('log_'));
            }
            if (empty($moduleManager->getModuleID())) {
                $moduleManager->setModuleID($this->generateUuidV4('mod_'));
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_module_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('module_manager/edit.html.twig', [
            'module_manager' => $moduleManager,
            'form' => $form,
            'page_title' => 'Edit Module Manager',
            'page_description' => 'Modify the settings and files of the selected module manager.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_module_manager_delete', methods: ['POST'])]
    public function delete(Request $request, ModuleManager $moduleManager, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $moduleManager->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($moduleManager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_module_manager_index', [], Response::HTTP_SEE_OTHER);
    }
}
