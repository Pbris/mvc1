<?php

namespace App\Deck;

class CardGraphic extends Card
{
    private $representation;

    public function __construct($suit, $value)
    {
        parent::__construct($suit, $value);
        $this->representation = $this->generateRepresentation();
    }

    private function generateRepresentation(): string
    {
        $symbols = [
            'Hearts'   => '♥',
            'Diamonds' => '♦',
            'Clubs'    => '♣',
            'Spades'   => '♠',
        ];

        return $symbols[$this->suit] . $this->value;
    }

    public function getAsString(): string
    {
        return $this->representation;
    }
}
