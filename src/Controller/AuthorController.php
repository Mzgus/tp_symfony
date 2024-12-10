<?php

namespace App\Controller;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Author;
use App\Form\AuthorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
class AuthorController extends AbstractController
{
    #[Route('/author_list', name: 'author_list', methods: ['GET'])]
    /**
     * @Route("/authors", name="author_list")
     */
    public function index(AuthorRepository $authorRepository): Response
    {
        ### Fonction qui permet d'afficher la liste des auteurs
        $authors = $authorRepository->findAll();
        return $this->render('author/index.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/new', name: 'author_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        ### Création de la fonction qui permet de créer un nouvel auteur
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('author_list');
        }

        return $this->render('author/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/author/{name}/edit', name: 'author_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Author $author, EntityManagerInterface $em): Response
    {
        ### Fonction qui permet d'edit un auteur
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('author_list');
        }

        return $this->render('author/edit.html.twig', [
            'form' => $form->createView(),
            'author' => $author,
        ]);
    }

    #[Route('/author/{name}', name: 'author_delete', methods: ['POST'])]
    public function delete(Request $request, Author $author, EntityManagerInterface $em): Response
    {
        ### Fonction qui permet de supprimer un auteur
        if ($this->isCsrfTokenValid('delete' . $author->getName(), $request->request->get('_token'))) {
            $em->remove($author);
            $em->flush();
        }

        return $this->redirectToRoute('author_list');
    }

}
