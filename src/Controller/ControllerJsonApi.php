<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Exception;

use App\Deck\Deck;
use App\Game\BlackjackGame;

class ControllerJsonApi
{
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
        // Check if deck is not in session
        if (!$session->has('deck')) {
            $deck = new Deck(); // Create a new deck of cards
            $session->set('deck', $deck);
        }

        // Retrieve deck from session
        $deck = $session->get('deck');
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
        // Check if deck is not in session
        if (!$session->has('deck')) {
            $deck = new Deck();
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
}
