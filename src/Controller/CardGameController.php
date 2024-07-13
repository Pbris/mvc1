<?php

namespace App\Controller;

use App\Card\Deck;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class CardGameController extends AbstractController
{
    #[Route("/card", name: "card_start")]
    public function home(): Response
    {
        return $this->render('card/home.html.twig');
    }

    #[Route('/card/deck', name: 'show_cards')]
    public function showCards(SessionInterface $session): Response
    {
        $deck = new Deck();
        $session->set('deck', $deck);

        return $this->render('card/deck.html.twig', [
            'deck' => $deck,
        ]);
    }

    #[Route('/card/deck/shuffle', name: 'shuffle_deck')]
    public function shuffleDeck(SessionInterface $session): Response
    {
        $deck = new Deck();
        $deck->shuffle();

        $session->set('deck', $deck);

        return $this->render('card/shuffle.html.twig', [
            'deck' => $deck,
        ]);
    }

    #[Route('/card/deck/draw', name: 'draw_card')]
    public function drawCard(SessionInterface $session): Response
    {
        // Check if deck is not in session
        if (!$session->has('deck')) {
            $deck = new Deck(); // Create a new deck of cards
            $session->set('deck', $deck);
        }

        // Retrieve deck from session
        $deck = $session->get('deck');


        if ($deck->remainingCardsCount() < 1) {
            throw new Exception("Cannot draw more cards. No cards left in deck.");
        }

        // Draw a card
        $drawnCard = $deck->drawCard();

        // Update the session
        $session->set('deck', $deck);

        return $this->render('card/draw.html.twig', [
            'drawnCard' => $drawnCard,
            'remainingCardsCount' => $deck->remainingCardsCount(),
        ]);
    }

    #[Route('/card/deck/draw/{number<\d+>}', name: 'draw_cards')]
    public function drawCards(int $number, SessionInterface $session): Response
    {
        // Check if deck is not in session
        if (!$session->has('deck')) {
            $deck = new Deck(); // Create a new deck of cards
            $session->set('deck', $deck);
        }

        // Retrieve deck from session
        $deck = $session->get('deck');

        if ($number > $deck->remainingCardsCount()) {
            throw new Exception("Cannot draw $number cards. Only {$deck->remainingCardsCount()} cards left in deck.");
        }

        // Draw the number of cards from the deck
        $drawnCards = [];
        for ($i = 0; $i < $number; $i++) {
            $drawnCard = $deck->drawCard();
            $drawnCards[] = $drawnCard;
        }

        // Update the session
        $session->set('deck', $deck);

        return $this->render('card/draw_many.html.twig', [
            'drawnCards' => $drawnCards,
            'remainingCardsCount' => $deck->remainingCardsCount(),
        ]);
    }

}
