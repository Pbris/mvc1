<?php

namespace App\Card;

class DeckProj
{
    protected $cards = [];

    public function __construct($numberOfDecks = 1)
    {
        $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
        $values = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King', 'Ace'];

        for ($i = 0; $i < $numberOfDecks; $i++) {
            foreach ($suits as $suit) {
                foreach ($values as $value) {
                    $this->cards[] = new CardGraphic($suit, $value);
                }
            }
        }
    }

    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    public function drawCard(): ?Card
    {
        return array_pop($this->cards) ?? null;
    }

    public function remainingCardsCount(): int
    {
        return count($this->cards);
    }

    public function getCards(): array
    {
        return $this->cards;
    }
}
