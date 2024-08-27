<?php

namespace App\GameProj;

use App\Card\DeckProj;
use App\Card\Card;
use InvalidArgumentException;
use App\GameProj\BlackjackHandHelperProj;

/**
 * Manages an advanced Blackjack game with multiple hands and computer player support.
 */
class BlackjackGameProj
{
    /** @var DeckProj The deck of cards */
    private $deck;

    /** @var BlackjackHandManagerProj Manages the hands in the game */
    private $handManager;

    /** @var string The name of the player */
    private $playerName;

    /** @var float The player bank balance */
    private $playerBank;

    /** @var int|null The index of the computer hand */
    private $computerHandIndex;

    /** @var string The strategy used by the computer player */
    private $computerStrategy;

    /**
     * BlackjackGameProj constructor.
     *
     * @param string $playerName The name of the player
     * @param float $playerBank The initial amount in the player bank
     * @param int $numberOfHands The number of hands to be played
     * @param float $totalBet The total bet amount for all hands
     * @param int|null $computerHandIndex The index of the computer hand, or null if not used
     * @param string $computerStrategy The strategy used by the computer player
     */
    public function __construct(string $playerName, float $playerBank, int $numberOfHands, float $totalBet, int $computerHandIndex = null, string $computerStrategy = 'dumb')
    {
        $this->deck = new DeckProj(4);
        $this->deck->shuffle();

        $this->handManager = new BlackjackHandManagerProj($numberOfHands, $totalBet);
        $this->playerName = $playerName;
        $this->playerBank = $playerBank;
        $this->playerBank -= $totalBet;

        $this->computerHandIndex = $computerHandIndex;
        $this->computerStrategy = $computerStrategy;
    }

    /**
     * Deal initial cards to all hands.
     */
    public function dealInitialCards(): void
    {
        $this->handManager->dealInitialCards($this->deck);
    }

    /**
     * Draw a card for a specific hand.
     *
     * @param int $handIndex The index of the hand to hit
     */
    public function hitHand(int $handIndex): void
    {
        $this->handManager->hitHand($handIndex, $this->deck);
    }

    /**
     * Stand a specific hand.
     *
     * @param int $handIndex The index of the hand to stand
     */
    public function standHand(int $handIndex): void
    {
        $this->handManager->standHand($handIndex);
    }

    /**
     * Draw a card for the dealer.
     */
    public function hitDealer(): void
    {
        $this->handManager->hitDealer($this->deck);
    }

    /**
     * Get all player hands.
     *
     * @return array[] An array of player hands
     */
    public function getHands(): array
    {
        return $this->handManager->getPlayerHands();
    }

    /**
     * Get dealer hand.
     *
     * @return Card[] The dealer hand
     */
    public function getDealerHand(): array
    {
        return $this->handManager->getDealerHand();
    }

    /**
     * Calculate the total value of a hand.
     *
     * @param Card[] $hand The hand to calculate
     * @return int The value of the hand
     */
    public function getHandValue(array $hand): int
    {
        return $this->handManager->getHandValue($hand);
    }

    /**
     * Check if the dealer must draw more cards.
     *
     * @return bool True if dealer hand value is below 17
     */
    public function dealerMustDraw(): bool
    {
        return $this->handManager->dealerMustDraw();
    }

    /**
     * Check if a specific hand is busted.
     *
     * @param int $handIndex The index of the hand to check
     * @return bool True if the hand is busted
     */
    public function isHandBusted(int $handIndex): bool
    {
        return $this->handManager->isHandBusted($handIndex);
    }

    /**
     * Check if the game is over.
     *
     * @return bool True if the game is over
     */
    public function isGameOver(): bool
    {
        return $this->handManager->isGameOver($this->computerHandIndex);
    }

    /**
     * Finish the game by playing the computer hand and settling bets.
     */
    public function finishGame(): void
    {
        $this->playComputerHand();
        while ($this->dealerMustDraw()) {
            $this->hitDealer();
        }
        $this->settleBets();
    }

    /**
     * Get the probability of busting for a specific hand.
     *
     * @param int $handIndex The index of the hand to check
     * @return float The probability of busting
     */
    public function getBustProbability(int $handIndex): float
    {
        return $this->handManager->getBustProbability($handIndex, $this->deck);
    }

    /**
     * Get the index of the current hand being played.
     *
     * @return int|null The index of the current hand, or null if no hands are playing
     */
    public function getCurrentHand(): ?int
    {
        return $this->handManager->getCurrentHand();
    }

    /**
     * Move to the next hand.
     */
    public function nextHand(): void
    {
        $this->handManager->nextHand();
    }

    /**
     * Play the computer hand according to its strategy.
     */
    public function playComputerHand(): void
    {
        $this->handManager->playComputerHand($this->computerHandIndex, $this->computerStrategy, $this->deck);
    }

    /**
     * Check for blackjack in all hands.
     */
    public function checkForBlackjack(): void
    {
        $this->handManager->checkForBlackjack();
    }

    /**
     * Check if a hand has blackjack.
     *
     * @param Card[] $hand The hand to check
     * @return bool True if the hand has blackjack
     */
    public function hasBlackjack(array $hand): bool
    {
        return $this->handManager->hasBlackjack($hand);
    }

    /**
     * Settle all bets and update the player bank.
     */
    public function settleBets(): void
    {
        $bankAdjustment = $this->handManager->settleBets();
        $this->playerBank += $bankAdjustment;
    }

    /**
     * Get the current balance in the player bank.
     *
     * @return float The player bank balance
     */
    public function getPlayerBank(): float
    {
        return $this->playerBank;
    }

    /**
     * Get the player name.
     *
     * @return string The player name
     */
    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    /**
     * Check if a specific hand is controlled by the computer.
     *
     * @param int $handIndex The index of the hand to check
     * @return bool True if the hand is controlled by the computer
     */
    public function isComputerHand(int $handIndex): bool
    {
        return $handIndex === $this->computerHandIndex;
    }

    /**
     * For testing, set a specific player hand.
     *
     * @param array $hand The hand to set
     * @param int $index The index of the hand to set
     */
    public function setPlayerHand(array $hand, int $index = 0)
    {
        $this->handManager->setPlayerHand($hand, $index);
    }

    /**
     * For testing, set a specific dealer hand.
     *
     * @param array $hand The hand to set
     */
    public function setDealerHand(array $hand)
    {
        $this->handManager->setDealerHand($hand);
    }
}
