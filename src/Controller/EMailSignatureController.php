<?php
namespace App\Controller;

use App\Service\SignatureGenerator;
use App\Repository\EMailSignatureRepository;
use App\Form\EMailSignatureForm;
use App\Entity\EMailSignature;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;


#[Route('/admin/email-signature')]
final class EMailSignatureController extends AbstractController
{
   private SignatureGenerator $signatureGenerator;
    private string $projectDir;

    // Autowiring der benötigten Services über den Konstruktor
    public function __construct(SignatureGenerator $signatureGenerator, ParameterBagInterface $params)
    {
        $this->signatureGenerator = $signatureGenerator;
        $this->projectDir = $params->get('kernel.project_dir');  // Get project directory
    }

    #[Route(name: 'app_email_signature_index', methods: ['GET'])]
    public function index(EMailSignatureRepository $eMailSignatureRepository): Response
    {
        return $this->render('e_mail_signature/index.html.twig', [
            'e_mail_signatures' => $eMailSignatureRepository->findAll(),
            'page_title' => 'E-Mail Signaturen',
            'page_description' => 'Hier können Sie E-Mail Signaturen verwalten.',
        ]);
    }

    #[Route('/new', name: 'app_email_signature_new', methods: ['GET', 'POST'])]
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    SignatureGenerator $signatureGenerator
): Response {
    $emailSignature = new EmailSignature();

    $form = $this->createForm(EMailSignatureForm::class, $emailSignature);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // HTML generieren und setzen
        $html = $signatureGenerator->generate($emailSignature);
        $emailSignature->setHtmlOutput($html);

        $entityManager->persist($emailSignature);
        $entityManager->flush();

        return $this->redirectToRoute('app_email_signature_index');
    }

    return $this->render('e_mail_signature/new.html.twig', [
        'form' => $form->createView(),
        'page_title' => 'Neue E-Mail Signatur',
        'page_description' => 'Hier können Sie eine neue E-Mail Signatur erstellen.',
    ]);
}

    #[Route('/{id<\d+>}', name: 'app_email_signature_show', methods: ['GET'])]
    public function show(EMailSignature $eMailSignature): Response
    {
        return $this->render('e_mail_signature/show.html.twig', [
            'e_mail_signature' => $eMailSignature,
            'page_title' => 'E-Mail Signatur anzeigen',
            'page_description' => 'Hier können Sie die Details der E-Mail Signatur anzeigen.',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_email_signature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EMailSignature $eMailSignature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EMailSignatureForm::class, $eMailSignature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_email_signature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('e_mail_signature/edit.html.twig', [
            'e_mail_signature' => $eMailSignature,
            'form' => $form,
            'page_title' => 'E-Mail Signatur bearbeiten',
            'page_description' => 'Hier können Sie die E-Mail Signatur bearbeiten.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_email_signature_delete', methods: ['POST'])]
    public function delete(Request $request, EMailSignature $eMailSignature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eMailSignature->getId(), $request->request->get('_token'))) {
            $entityManager->remove($eMailSignature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_email_signature_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(
    '/signature/preview',
    name: 'signature_preview',
    methods: ['POST'],
    defaults: ['_format' => 'json']
)]
public function preview(Request $request): JsonResponse
{
    // Erstelle das Form-Objekt für die E-Mail-Signatur
    $data = new EMailSignature();
    $form = $this->createForm(EMailSignatureForm::class, $data);

    // Verarbeite die Formulardaten
    $form->handleRequest($request);

    // Holen der benutzerdefinierten HTML-Ausgabe (htmlOutput) aus dem Request
    $htmlOutput = $request->request->get('htmlOutput'); // Hier den tatsächlichen Namen des Eingabefeldes verwenden

    // Falls htmlOutput vorhanden ist, überschreibt es den HTML-Output der generierten Signatur
    if ($htmlOutput) {
        $data->setHtmlOutput($htmlOutput);
    }

    // Generiere die HTML-Signatur basierend auf den Formulardaten
    $signatureHtml = $this->signatureGenerator->generate($data);

    return $this->json([
        'html' => $signatureHtml, // Gibt den generierten HTML-Code zurück
    ]);
}

}
