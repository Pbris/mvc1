<?php

namespace App\GameProj;

use App\Card\DeckProj;
use App\Card\Card;

/**
 * Helper class for more advanced Blackjack hand operations.
 */
class BlackjackHandHelperProj
{
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
            if ($cardValue === 11) {
                $aceCount++;
            }
            $value += $cardValue;
        }

        while ($value > 21 && $aceCount > 0) {
            $value -= 10;
            $aceCount--;
        }

        return $value;
    }

    /**
     * Get the probability of busting for a specific hand.
     *
     * @param array $hand The hand to check
     * @param DeckProj $deck The deck to calculate probabilities from
     * @return float The probability of busting
     */
    public function getBustProbability(array $hand, DeckProj $deck): float
    {
        $handValue = $this->getHandValue($hand);

        $hasAce = $this->handHasAce($hand);
        $lowHandValue = $this->getLowHandValue($hand);

        if ($hasAce && $handValue != $lowHandValue) {
            $handValue = $lowHandValue;
        }

        $safeValue = 21 - $handValue;

        if ($safeValue <= 0) {
            return 1.0;
        }

        if ($hasAce && $handValue <= 11) {
            return 0.0;
        }

        $totalCards = $deck->remainingCardsCount();
        $safeCards = 0;

        foreach ($deck->getCards() as $card) {
            $cardValue = $card->getBlackjackValue();
            $isAce = $card->getValue() === 'Ace';

            if ($cardValue <= $safeValue || $isAce) {
                $safeCards++;
            }
        }

        return 1 - ($safeCards / $totalCards);
    }

    /**
     * Check if a hand has blackjack.
     *
     * @param array $hand The hand to check
     * @return bool True if the hand has blackjack
     */
    public function hasBlackjack(array $hand): bool
    {
        return count($hand) == 2 && $this->getHandValue($hand) == 21;
    }

    /**
     * Check if a hand contains an Ace.
     *
     * @param Card[] $hand The hand to check
     * @return bool True if the hand contains an Ace
     */
    private function handHasAce(array $hand): bool
    {
        foreach ($hand as $card) {
            if ($card->getValue() === 'Ace') {
                return true;
            }
        }
        return false;
    }

    /**
     * Calculate the low value of a hand (counting Aces as 1).
     *
     * @param Card[] $hand The hand to calculate
     * @return int The low value of the hand
     */
    private function getLowHandValue(array $hand): int
    {
        $lowValue = 0;
        foreach ($hand as $card) {
            if ($card->getValue() === 'Ace') {
                $lowValue += 1;
                continue;
            }

            $lowValue += $card->getBlackjackValue();
        }
        return $lowValue;
    }

    /**
     * Determine if the computer should hit based on its strategy.
     *
     * @param string $computerStrategy The strategy used by the computer player
     * @param int $handValue The current value of the hand
     * @param array $hand The current hand
     * @return bool True if the computer should hit
     */
    public function shouldComputerHit(string $computerStrategy, int $handValue, array $hand): bool
    {
        if ($computerStrategy === 'smart') {
            if ($handValue < 17) {
                return true;
            }
            if ($handValue < 18 && $this->handHasAce($hand)) {
                return true;
            }
            return false;
        }

        return $handValue < 17;
    }

    /**
     * Settle all bets.
     *
     * @param array $hands All player hands
     * @param array $dealerHand The dealer hand
     * @return float The total adjustment to the player bank
     */
    public function settleBets(array $hands, array $dealerHand): float
    {
        $dealerBlackjack = $this->hasBlackjack($dealerHand);
        $dealerValue = $this->getHandValue($dealerHand);
        $bankAdjustment = 0;

        foreach ($hands as $hand) {
            $bankAdjustment += $this->settleSingleHandBet($hand, $dealerBlackjack, $dealerValue);
        }

        return $bankAdjustment;
    }

    /**
     * Settle the bet for a single hand.
     *
     * @param array $hand The hand to settle
     * @param bool $dealerBlackjack Whether the dealer has blackjack
     * @param int $dealerValue The value of the dealer hand
     * @return float The adjustment to the player bank for this hand
     */
    private function settleSingleHandBet(array $hand, bool $dealerBlackjack, int $dealerValue): float
    {
        $handValue = $this->getHandValue($hand['hand']);
        $playerBlackjack = $hand['status'] === 'blackjack';

        if ($playerBlackjack) {
            if ($dealerBlackjack) {
                return $hand['bet'];
            }
            return $hand['bet'] * 2.5;
        }

        if ($handValue == $dealerValue) {
            return $hand['bet'];
        }

        if ($handValue <= 21 && ($handValue > $dealerValue || $dealerValue > 21)) {
            return $hand['bet'] * 2;
        }

        return 0;
    }
}
