<?php

namespace App\GameProj;

use App\Card\DeckProj;
use App\Card\Card;
use InvalidArgumentException;

/**
 * Manages the hands in a Blackjack game.
 */
class BlackjackHandManagerProj
{
    /** @var array[] The player hands */
    private $hands = [];

    /** @var Card[] The dealer hand */
    private $dealerHand = [];

    /** @var int The index of the current hand being played */
    private $currentHandIndex = 0;

    /** @var BlackjackHandHelperProj */
    private $helper;

    /**
     * BlackjackHandManager constructor.
     *
     * @param int $numberOfHands The number of hands to manage
     * @param float $totalBet The total bet amount for all hands
     * @throws InvalidArgumentException If the number of hands is invalid
     */
    public function __construct(int $numberOfHands, float $totalBet)
    {
        if ($numberOfHands < 1 || $numberOfHands > 3) {
            throw new InvalidArgumentException("Number of hands must be between 1 and 3");
        }

        $betPerHand = $totalBet / $numberOfHands;
        for ($i = 0; $i < $numberOfHands; $i++) {
            $this->hands[] = [
                'hand' => [],
                'status' => 'playing',
                'bet' => $betPerHand
            ];
        }

        $this->helper = new BlackjackHandHelperProj();
    }

    /**
     * Deal initial cards to all hands.
     *
     * @param DeckProj $deck The deck to draw cards from
     */
    public function dealInitialCards(DeckProj $deck): void
    {
        foreach ($this->hands as &$hand) {
            $hand['hand'][] = $deck->drawCard();
            $hand['hand'][] = $deck->drawCard();
        }
        $this->dealerHand[] = $deck->drawCard();
    }

    /**
     * Draw a card for a specific hand.
     *
     * @param int $handIndex The index of the hand to hit
     * @param DeckProj $deck The deck to draw cards from
     * @throws InvalidArgumentException If the hand index is invalid
     */
    public function hitHand(int $handIndex, DeckProj $deck): void
    {
        if ($handIndex < 0 || $handIndex >= count($this->hands)) {
            throw new InvalidArgumentException("Invalid hand index");
        }

        $this->hands[$handIndex]['hand'][] = $deck->drawCard();
        if ($this->getHandValue($this->hands[$handIndex]['hand']) > 21) {
            $this->hands[$handIndex]['status'] = 'busted';
        }
    }

    /**
     * Stand a specific hand.
     *
     * @param int $handIndex The index of the hand to stand
     * @throws InvalidArgumentException If the hand index is invalid
     */
    public function standHand(int $handIndex): void
    {
        if ($handIndex < 0 || $handIndex >= count($this->hands)) {
            throw new InvalidArgumentException("Invalid hand index");
        }

        $this->hands[$handIndex]['status'] = 'standing';
    }

    /**
     * Draw a card for the dealer.
     *
     * @param DeckProj $deck The deck to draw cards from
     */
    public function hitDealer(DeckProj $deck): void
    {
        $this->dealerHand[] = $deck->drawCard();
    }

    /**
     * Get all player hands.
     *
     * @return array[] An array of player hands
     */
    public function getPlayerHands(): array
    {
        return $this->hands;
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
        return $this->helper->getHandValue($hand);
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
     * Check if a specific hand is busted.
     *
     * @param int $handIndex The index of the hand to check
     * @return bool True if the hand is busted
     */
    public function isHandBusted(int $handIndex): bool
    {
        return $this->hands[$handIndex]['status'] === 'busted';
    }

    /**
     * Check if the game is over.
     *
     * @param int|null $computerHandIndex The index of the computer hand, or null if not used
     * @return bool True if the game is over
     */
    public function isGameOver(?int $computerHandIndex): bool
    {
        foreach ($this->hands as $index => $hand) {
            if ($hand['status'] === 'playing' && $index !== $computerHandIndex) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the probability of busting for a specific hand.
     *
     * @param int $handIndex The index of the hand to check
     * @param DeckProj $deck The deck to calculate probabilities from
     * @return float The probability of busting
     */
    public function getBustProbability(int $handIndex, DeckProj $deck): float
    {
        return $this->helper->getBustProbability($this->hands[$handIndex]['hand'], $deck);
    }

    /**
     * Get the index of the current hand being played.
     *
     * @return int|null The index of the current hand, or null if no hands are playing
     */
    public function getCurrentHand(): ?int
    {
        $totalHands = count($this->hands);

        while ($this->currentHandIndex < $totalHands) {
            if ($this->hands[$this->currentHandIndex]['status'] === 'playing') {
                return $this->currentHandIndex;
            }
            $this->currentHandIndex++;
        }
        return null;
    }

    /**
     * Move to the next hand.
     */
    public function nextHand(): void
    {
        $this->currentHandIndex++;
        if ($this->currentHandIndex >= count($this->hands)) {
            $this->currentHandIndex = 0;
        }
    }

    /**
     * Play the computer hand according to its strategy.
     *
     * @param int|null $computerHandIndex The index of the computer hand
     * @param string $computerStrategy The strategy used by the computer player
     * @param DeckProj $deck The deck to draw cards from
     */
    public function playComputerHand(?int $computerHandIndex, string $computerStrategy, DeckProj $deck): void
    {
        if ($computerHandIndex === null) {
            return;
        }
    
        $hand = &$this->hands[$computerHandIndex];
        if ($hand['status'] !== 'playing') {
            return;
        }
    
        while ($hand['status'] === 'playing') {
            $handValue = $this->getHandValue($hand['hand']);
    
            if ($this->helper->shouldComputerHit($computerStrategy, $handValue, $hand['hand'])) {
                $hand['hand'][] = $deck->drawCard();
                if ($this->getHandValue($hand['hand']) > 21) {
                    $hand['status'] = 'busted';
                }
                continue;
            }

            $hand['status'] = 'standing';
        }
    }

    /**
     * Check for blackjack in all hands.
     */
    public function checkForBlackjack(): void
    {
        foreach ($this->hands as &$hand) {
            if ($this->hasBlackjack($hand['hand'])) {
                $hand['status'] = 'blackjack';
            }
        }

        if ($this->hasBlackjack($this->dealerHand)) {
            $this->dealerHand['status'] = 'blackjack';
        }
    }

    /**
     * Check if a hand has blackjack.
     *
     * @param Card[] $hand The hand to check
     * @return bool True if the hand has blackjack
     */
    public function hasBlackjack(array $hand): bool
    {
        return $this->helper->hasBlackjack($hand);
    }

    /**
     * Settle all bets.
     *
     * @return float The total adjustment to the player bank
     */
    public function settleBets(): float
    {
        return $this->helper->settleBets($this->hands, $this->dealerHand);
    }

    /**
     * For testing, set a specific player hand.
     *
     * @param array $hand The hand to set
     * @param int $index The index of the hand to set
     */
    public function setPlayerHand(array $hand, int $index = 0)
    {
        $this->hands[$index]['hand'] = $hand;
    }

    /**
     * For testing, set a specific dealer hand.
     *
     * @param array $hand The hand to set
     */
    public function setDealerHand(array $hand)
    {
        $this->dealerHand = $hand;
    }
}
