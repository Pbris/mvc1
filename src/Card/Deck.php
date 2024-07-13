<?php

namespace App\Card;

class Deck
{
    protected $cards = [];

    public function __construct()
    {
        $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King', 'Ace'];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $this->cards[$suit][] = new CardGraphic($suit, $value);
            }
        }
    }

    public function shuffle(): void
    {
        foreach ($this->cards as &$cards) {
            shuffle($cards);
        }
    }

    public function drawCard(): ?Card
    {
        // Get a random suit from available suits
        $suits = array_keys($this->cards);
        $randomSuit = $suits[array_rand($suits)];

        shuffle($this->cards[$randomSuit]);

        // Draw a card from the suit
        $card = array_shift($this->cards[$randomSuit]);

        // If suit is empty, remove it from deck
        if (empty($this->cards[$randomSuit])) {
            unset($this->cards[$randomSuit]);
        }

        return $card;
    }



    public function remainingCardsCount(): int
    {
        $totalCards = 0;
        foreach ($this->cards as $cardsInSuit) {
            $totalCards += count($cardsInSuit);
        }
        return $totalCards;
    }

    public function getCards(): array
    {
        return $this->cards;
    }
}
