<?php

namespace App\Game;

/**
 * Manages a Blackjack game.
 */
class BlackjackGame
{
    /** @var Deck The deck of cards */
    private $deck;

    /** @var Card[] The player hand */
    private $playerHand = [];

    /** @var Card[] The dealer hand */
    private $dealerHand = [];

    /**
     * BlackjackGame constructor.
     */
    public function __construct()
    {
        $this->deck = new Deck();
        $this->deck->shuffle();
    }

    /**
     * Deal first cards to player and dealer.
     */
    public function dealCards(): void
    {
        $this->playerHand[] = $this->deck->drawCard();
        $this->playerHand[] = $this->deck->drawCard();
        $this->dealerHand[] = $this->deck->drawCard();
    }

    /**
     * Draw a card for the player.
     */
    public function hitPlayer(): void
    {
        $this->playerHand[] = $this->deck->drawCard();
    }

    /**
     * Draw a card for the dealer.
     */
    public function hitDealer(): void
    {
        $this->dealerHand[] = $this->deck->drawCard();
    }

    /**
     * Get player hand.
     *
     * @return Card[] The player hand
     */
    public function getPlayerHand(): array
    {
        return $this->playerHand;
    }

    /**
     * Get dealer hand.
     *
     * @return Card[] The dealer hand
     */
    public function getDealerHand(): array
    {
        return $this->dealerHand;
    }

    /**
     * Calculate the total value of a hand.
     *
     * @param Card[] $hand The hand to calculate
     * @return int The value of the hand
     */
    public function getHandValue(array $hand): int
    {
        $value = 0;
        $aceCount = 0;

        foreach ($hand as $card) {
            $cardValue = $card->getBlackjackValue();
            $value += $cardValue;
            if ($card->getValue() == 'Ace') {
                $aceCount++;
            }
        }

        while ($value > 21 && $aceCount > 0) {
            $value -= 10;
            $aceCount--;
        }

        return $value;
    }

    /**
     * Check if the dealer must draw more cards.
     *
     * @return bool True if dealer hand value is below 17
     */
    public function dealerMustDraw(): bool
    {
        return $this->getHandValue($this->dealerHand) < 17;
    }

    /**
     * Check if the player has busted.
     *
     * @return bool True if player hand value exceeds 21
     */
    public function isPlayerBusted(): bool
    {
        return $this->getHandValue($this->playerHand) > 21;
    }
}
