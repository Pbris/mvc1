<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller for managing library.
 */
class BookController extends AbstractController
{
    /**
     * Landing page.
     */
    #[Route('/library', name: 'app_library')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
    /**
     * Displays form for creating a new book.
     */
    #[Route('/book/create', name: 'book_create', methods: ['GET'])]
    public function createBookForm(): Response
    {
        return $this->render('book/create.html.twig');
    }

    /**
     * Handles creation of new book.
     */
    #[Route('/book/create', name: 'book_create_post', methods: ['POST'])]
    public function createBook(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $book = new Book();
        $book->setTitle($request->request->get('title'));
        $book->setIsbn($request->request->get('isbn'));
        $book->setAuthor($request->request->get('author'));
        $book->setImage($request->request->get('image_filename'));

        $entityManager->persist($book);
        $entityManager->flush();

        return new Response('Saved new book with id ' . $book->getId());
    }
    /**
     * Display a book by ID.
     */
    #[Route('/book/show/{id}', name: 'book_by_id')]
    public function showBookById(
        bookRepository $bookRepository,
        int $id
    ): Response {
        $book = $bookRepository
            ->find($id);

        return $this->render('book/show_one.html.twig', [
            'book' => $book,
        ]);
    }
    /**
     * Display all books.
     */
    #[Route('/book/show', name: 'book_show_all')]
    public function showAllBooks(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();

        return $this->render('book/show_all.html.twig', [
            'books' => $books,
        ]);
    }
    /**
     * Delete a book by ID.
     */
    #[Route('/book/delete/{id}', name: 'book_delete_by_id')]
    public function deleteBookById(
        ManagerRegistry $doctrine,
        int $id
    ): Response {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(book::class)->find($id);

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('book_show_all');
    }
    /**
     * Display form for update of a book.
     */
    #[Route('/book/edit/{id}', name: 'book_edit', methods: ['GET'])]
    public function editBookForm(int $id, ManagerRegistry $doctrine): Response
    {
        $book = $doctrine->getRepository(Book::class)->find($id);

        return $this->render('book/edit.html.twig', [
            'book' => $book,
        ]);
    }
    /**
     * Handles update of a book.
     */
    #[Route('/book/update/{id}', name: 'book_update', methods: ['POST'])]
    public function updateBook(
        Request $request,
        ManagerRegistry $doctrine,
        int $id
    ): Response {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);

        $book->setTitle($request->request->get('title'));
        $book->setIsbn($request->request->get('isbn'));
        $book->setAuthor($request->request->get('author'));
        $book->setImage($request->request->get('image_filename'));

        $entityManager->flush();

        return $this->redirectToRoute('book_show_all');
    }
}
