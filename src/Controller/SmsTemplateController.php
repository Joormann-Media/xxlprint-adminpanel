<?php

namespace App\Controller;

use App\Entity\SmsTemplate;
use App\Form\SmsTemplateType;
use App\Repository\SmsTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\SmsSenderService;
use Symfony\Component\Form\Extension\Core\Type\TextType;



#[Route('/admin/sms-template')]
final class SmsTemplateController extends AbstractController
{
    #[Route(name: 'app_sms_template_index', methods: ['GET'])]
    public function index(SmsTemplateRepository $smsTemplateRepository): Response
    {
        return $this->render('sms_template/index.html.twig', [
            'sms_templates' => $smsTemplateRepository->findAll(),
            'page_title' => 'SMS-Vorlagen',
            'page_description' => 'Verwalten Sie Ihre SMS-Vorlagen.',
            'page_icon' => 'fa-solid fa-sms',
        ]);
    }

    #[Route('/new', name: 'app_sms_template_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $smsTemplate = new SmsTemplate();
        $form = $this->createForm(SmsTemplateType::class, $smsTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($smsTemplate);
            $entityManager->flush();

            $this->addFlash('success', '📨 SMS-Vorlage wurde erfolgreich erstellt.');
            return $this->redirectToRoute('app_sms_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sms_template/new.html.twig', [
            'sms_template' => $smsTemplate,
            'form' => $form,
            'page_title' => 'Neue SMS-Vorlage',
            'page_description' => 'Erstellen Sie eine neue SMS-Vorlage.',
            'page_icon' => 'fa-solid fa-plus',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_sms_template_show', methods: ['GET'])]
    public function show(SmsTemplate $smsTemplate): Response
    {
        return $this->render('sms_template/show.html.twig', [
            'sms_template' => $smsTemplate,
            'page_title' => 'SMS-Vorlage anzeigen',
            'page_description' => 'Details zur SMS-Vorlage.',
            'page_icon' => 'fa-solid fa-eye',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_sms_template_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SmsTemplate $smsTemplate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SmsTemplateType::class, $smsTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sms_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sms_template/edit.html.twig', [
            'sms_template' => $smsTemplate,
            'form' => $form,
            'page_title' => 'SMS-Vorlage bearbeiten',
            'page_description' => 'Bearbeiten Sie die SMS-Vorlage.',
            'page_icon' => 'fa-solid fa-edit',
            'page_icon_color' => 'text-warning',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_sms_template_delete', methods: ['POST'])]
public function delete(Request $request, SmsTemplate $smsTemplate, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete' . $smsTemplate->getId(), $request->request->get('_token'))) {
        $entityManager->remove($smsTemplate);
        $entityManager->flush();

        $this->addFlash('success', '📨 SMS-Vorlage wurde erfolgreich gelöscht.');
    } else {
        $this->addFlash('danger', '⚠️ Ungültiges CSRF-Token. Löschen nicht möglich.');
    }

    return $this->redirectToRoute('app_sms_template_index', [], Response::HTTP_SEE_OTHER);
}
#[Route('/{id<\d+>}/send', name: 'app_sms_template_send', methods: ['GET', 'POST'])]
public function send(
    SmsTemplate $smsTemplate,
    Request $request,
    SmsSenderService $smsSender
): Response {
    $defaultRecipient = '';
    $form = $this->createFormBuilder()
        ->add('recipient', TextType::class, [
            'label' => 'Empfänger (z. B. +49176...)',
            'data' => $defaultRecipient,
            'attr' => ['class' => 'form-control']
        ])
        ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $recipient = $form->get('recipient')->getData();
        try {
            $response = $smsSender->sendSms($recipient, $smsTemplate->getMessage());
            $this->addFlash('success', '✅ SMS erfolgreich versendet.');
        } catch (\Throwable $e) {
            $this->addFlash('danger', '❌ Fehler beim Versenden: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_sms_template_index');
    }

    return $this->render('sms_template/send.html.twig', [
        'sms_template' => $smsTemplate,
        'form' => $form->createView(),
        'page_title' => 'SMS-Vorlage senden',
        'page_description' => 'Senden Sie die SMS-Vorlage an einen Empfänger.',
    ]);
}
}
