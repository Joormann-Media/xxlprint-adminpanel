<?php

namespace App\Controller;

use App\Entity\PopUpCategory;
use App\Repository\PopUpCategoryRepository;
use App\Form\PopUpCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/popup-manager/category')]
final class PopUpCategoryController extends AbstractController
{
    #[Route(name: 'app_pop_up_category_index', methods: ['GET'])]
    public function index(PopUpCategoryRepository $popUpCategoryRepository): Response
    {
        return $this->render('pop_up_category/index.html.twig', [
            'pop_up_categories' => $popUpCategoryRepository->findAll(),
            'page_title' => 'PopUp Kategorien',
            'page_description' => 'Hier können Sie die PopUp Kategorien verwalten.',
            'page_icon' => 'fa-solid fa-tags',
        ]);
    }

    #[Route('/new', name: 'app_pop_up_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $popUpCategory = new PopUpCategory();
        $form = $this->createForm(PopUpCategoryType::class, $popUpCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($popUpCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_pop_up_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pop_up_category/new.html.twig', [
            'pop_up_category' => $popUpCategory,
            'form' => $form,
            'page_title' => 'Neue PopUp Kategorie erstellen',
            'page_description' => 'Hier können Sie eine neue PopUp Kategorie erstellen.',
            'page_icon' => 'fa-solid fa-plus',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_pop_up_category_show', methods: ['GET'])]
    public function show(PopUpCategory $popUpCategory): Response
    {
        return $this->render('pop_up_category/show.html.twig', [
            'pop_up_category' => $popUpCategory,
            'page_title' => 'PopUp Kategorie anzeigen',
            'page_description' => 'Hier können Sie die Details der PopUp Kategorie anzeigen.',
            'page_icon' => 'fa-solid fa-eye',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_pop_up_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PopUpCategory $popUpCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PopUpCategoryType::class, $popUpCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pop_up_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pop_up_category/edit.html.twig', [
            'pop_up_category' => $popUpCategory,
            'form' => $form,
            'page_title' => 'PopUp Kategorie bearbeiten',
            'page_description' => 'Hier können Sie die PopUp Kategorie bearbeiten.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_pop_up_category_delete', methods: ['POST'])]
public function delete(Request $request, PopUpCategory $popUpCategory, EntityManagerInterface $entityManager): Response
{
    // Validiert den CSRF-Token
    if ($this->isCsrfTokenValid('delete'.$popUpCategory->getId(), $request->request->get('_token'))) {
        $entityManager->remove($popUpCategory);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_pop_up_category_index', [], Response::HTTP_SEE_OTHER);
}

}
