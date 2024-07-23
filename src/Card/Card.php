<?php

namespace App\Card;

/**
 * Represents a playing card.
 */
class Card
{
    /** @var string The suit of the card */
    protected $suit;

    /** @var string The value of the card */
    protected $value;

    /**
     * Card constructor.
     *
     * @param string $suit The suit of the card
     * @param string $value The value of the card
     */
    public function __construct($suit, $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    /**
     * Get the suit of the card.
     *
     * @return string
     */
    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * Get the value of the card.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get a string representation of the card.
     *
     * @return string
     */
    public function getAsString(): string
    {
        return "{$this->value} of {$this->suit}";
    }

    /**
     * Get the Blackjack value of the card.
     *
     * @return int
     */
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