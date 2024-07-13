<?php

namespace App\Card;

class Card
{
    protected $suit;
    protected $value;

    public function __construct($suit, $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getAsString(): string
    {
        return "{$this->value} of {$this->suit}";
    }

    public function getBlackjackValue(): int
    {
        if (is_numeric($this->value)) {
            return (int) $this->value;
        }

        if (in_array($this->value, ['Jack', 'Queen', 'King'])) {
            return 10;
        }

        return 11;
    }
}
