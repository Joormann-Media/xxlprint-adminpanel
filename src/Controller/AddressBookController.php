<?php

namespace App\Controller;

use App\Entity\AddressBook;
use App\Form\AddressBookForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/address-book')]
final class AddressBookController extends AbstractController
{
    #[Route(name: 'app_address_book_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $addressBooks = $entityManager
            ->getRepository(AddressBook::class)
            ->findAll();

        return $this->render('address_book/index.html.twig', [
            'address_books' => $addressBooks,
        ]);
    }

    #[Route('/new', name: 'app_address_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $addressBook = new AddressBook();
        $form = $this->createForm(AddressBookForm::class, $addressBook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($addressBook);
            $entityManager->flush();

            return $this->redirectToRoute('app_address_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('address_book/new.html.twig', [
            'address_book' => $addressBook,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_address_book_show', methods: ['GET'])]
    public function show(AddressBook $addressBook): Response
    {
        return $this->render('address_book/show.html.twig', [
            'address_book' => $addressBook,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_address_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AddressBook $addressBook, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AddressBookForm::class, $addressBook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_address_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('address_book/edit.html.twig', [
            'address_book' => $addressBook,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_address_book_delete', methods: ['POST'])]
    public function delete(Request $request, AddressBook $addressBook, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$addressBook->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($addressBook);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_address_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
