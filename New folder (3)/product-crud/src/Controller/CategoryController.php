<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    /**
     * LIST - Toon alle categorieën
     */
    #[Route('/', name: 'category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAllSorted();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * DETAIL - Toon details van een categorie met alle producten
     */
    #[Route('/{id}', name: 'category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * CREATE - Maak een nieuwe categorie aan
     */
    #[Route('/create/new', name: 'category_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Categorie succesvol aangemaakt!');
            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/form.html.twig', [
            'form' => $form,
            'title' => 'Nieuwe Categorie Aanmaken',
            'action' => 'create',
        ]);
    }

    /**
     * UPDATE - Wijzig een categorie
     */
    #[Route('/{id}/edit', name: 'category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Categorie succesvol bijgewerkt!');
            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render('category/form.html.twig', [
            'form' => $form,
            'category' => $category,
            'title' => 'Categorie Bewerken',
            'action' => 'edit',
        ]);
    }

    /**
     * DELETE - Verwijder een categorie
     */
    #[Route('/{id}/delete', name: 'category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash('success', 'Categorie succesvol verwijderd!');
        }

        return $this->redirectToRoute('category_index');
    }
}
