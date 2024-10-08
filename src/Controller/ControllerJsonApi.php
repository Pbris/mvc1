<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Card\Deck;
use App\Game\BlackjackGame;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Trait\DeckManagerTrait;

class ControllerJsonApi extends AbstractController
{
    use DeckManagerTrait;

    #[Route("/api/deck", methods: ['GET'])]
    public function getDeck(): Response
    {
        $deck = new Deck();

        $cards = [];
        foreach ($deck->getCards() as $suitCards) {
            foreach ($suitCards as $card) {
                $cards[] = [
                    'suit' => $card->getSuit(),
                    'value' => $card->getValue()
                ];
            }
        }

        $response = new JsonResponse(['cards' => $cards]);
        $response->setEncodingOptions(JSON_PRETTY_PRINT);

        return $response;
    }


    #[Route("/api/deck/shuffle", methods: ['POST', 'GET'])]
    public function shuffleDeck(SessionInterface $session): Response
    {
        // Retrieve deck from session
        $deck = $this->getOrCreateDeck($session);
        $deck->shuffle();

        // Update the session
        $session->set('deck', $deck);

        $cards = [];
        foreach ($deck->getCards() as $suitCards) {
            foreach ($suitCards as $card) {
                $cards[] = [
                    'suit' => $card->getSuit(),
                    'value' => $card->getValue()
                ];
            }
        }

        $response = new JsonResponse(['cards' => $cards]);
        $response->setEncodingOptions(JSON_PRETTY_PRINT);

        return $response;
    }

    #[Route('/api/deck/draw/{number}', name: 'draw_cards_api', methods: ['POST', 'GET'])]
    public function drawCards(int $number, SessionInterface $session): Response
    {
        // Retrieve deck from session
        $deck = $this->getOrCreateDeck($session);

        if ($number > $deck->remainingCardsCount()) {
            throw new Exception("Cannot draw $number cards. Only {$deck->remainingCardsCount()} cards left in deck.");
        }

        // Draw the number of cards from the deck
        $drawnCards = [];
        for ($i = 0; $i < $number; $i++) {
            $drawnCard = $deck->drawCard();
            $drawnCards[] = [
                'suit' => $drawnCard->getSuit(),
                'value' => $drawnCard->getValue()
            ];
        }

        $session->set('deck', $deck);

        $data = [
            'drawnCards' => $drawnCards,
            'remainingCardsCount' => $deck->remainingCardsCount()
        ];

        return new JsonResponse($data);
    }

    #[Route('/api/game/standings', methods: ['GET'])]
    public function getStandings(SessionInterface $session): Response
    {
        if (!$session->has('game')) {
            return new JsonResponse(['No game in progress']);
        }

        $game = $session->get('game');

        $data = [
            'playerValue' => $game->getHandValue($game->getPlayerHand()),
            'dealerValue' => $game->getHandValue($game->getDealerHand())
        ];

        return new JsonResponse($data);
    }

    #[Route('/api/library/books', name: 'api_library_books', methods: ['GET'])]
    public function getAllBooks(ManagerRegistry $doctrine): JsonResponse
    {
        $books = $doctrine->getRepository(Book::class)->findAll();

        $response = $this->json($books);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
        return $response;
    }

    #[Route('/api/library/book/{isbn}', name: 'api_library_book', methods: ['GET'])]
    public function getBookByIsbn(string $isbn, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->findByIsbn($isbn);

        $response = $this->json($book);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
        return $response;
    }
}
