<?php

namespace App\Game;

use App\Card\Deck;
use App\Card\Card;

class BlackjackGame
{
    private $deck;
    private $playerHand = [];
    private $dealerHand = [];

    public function __construct()
    {
        $this->deck = new Deck();
        $this->deck->shuffle();
    }

    public function dealCards(): void
    {
        $this->playerHand[] = $this->deck->drawCard();
        $this->playerHand[] = $this->deck->drawCard();
        $this->dealerHand[] = $this->deck->drawCard();
    }

    public function hitPlayer(): void
    {
        $this->playerHand[] = $this->deck->drawCard();
    }

    public function hitDealer(): void
    {
        $this->dealerHand[] = $this->deck->drawCard();
    }

    public function getPlayerHand(): array
    {
        return $this->playerHand;
    }

    public function getDealerHand(): array
    {
        return $this->dealerHand;
    }

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

    public function dealerMustDraw(): bool
    {
        return $this->getHandValue($this->dealerHand) < 17;
    }

    public function isPlayerBusted(): bool
    {
        return $this->getHandValue($this->playerHand) > 21;
    }
}
