<?php

// src/Controller/Admin/AddressImportController.php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class AddressImportController extends AbstractController
{
    #[Route('/admin/address/import', name: 'admin_address_import')]
    public function import(Request $request, KernelInterface $kernel): Response
    {
        // Lies PLZ aus dem Formular
        $plzInput = $request->request->get('plz'); // z.B. "46483 46485"
        $plzList = preg_split('/[\s,]+/', $plzInput, -1, PREG_SPLIT_NO_EMPTY);

        // Setup Symfony Console Application im Web-Kontext
        $application = new Application($kernel);
        $application->setAutoExit(false);

        // Übergib Argumente wie aus CLI
        $input = new ArrayInput([
            'command' => 'app:import-addresses-stapel',
            'plz' => $plzList,
        ]);
        $output = new BufferedOutput();

        // Run!
        $application->run($input, $output);

        $resultText = $output->fetch();

        // Zeig das Ergebnis im Adminpanel (oder logge/flash es)
        $this->addFlash('success', nl2br($resultText));

        return $this->redirectToRoute('admin_address_import');
    }
}

