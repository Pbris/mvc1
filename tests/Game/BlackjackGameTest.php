<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;
use App\Card\Deck;
use App\Card\Card;

/**
 * Test cases for class BlackjackGame.
 */
class BlackjackGameTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateBlackjackGame()
    {
        $game = new BlackjackGame();
        $this->assertInstanceOf("\App\Game\BlackjackGame", $game);

        $playerHand = $game->getPlayerHand();
        $this->assertEmpty($playerHand);

        $dealerHand = $game->getDealerHand();
        $this->assertEmpty($dealerHand);
    }
    /**
     * Test dealing cards.
     */
    public function testDealCards()
    {
        $game = new BlackjackGame();
        $game->dealCards();

        $playerHand = $game->getPlayerHand();
        $this->assertCount(2, $playerHand);

        $dealerHand = $game->getDealerHand();
        $this->assertCount(1, $dealerHand);
    }
    /**
     * Test hitting player and dealer.
     */
    public function testHit()
    {
        $game = new BlackjackGame();
        $game->dealCards();

        $game->hitPlayer();
        $playerHand = $game->getPlayerHand();
        $this->assertCount(3, $playerHand);

        $game->hitDealer();
        $dealerHand = $game->getDealerHand();
        $this->assertCount(2, $dealerHand);
    }
    /**
     * Test hand value, stub Card to assure the value can be asserted.
     */
    public function testGetHandValue()
    {
        $game = new BlackjackGame();

        // Create a stub for the Card class
        $stub = $this->createMock(Card::class);

        // Configure the stub
        $stub->method('getBlackjackValue')
             ->willReturn(7, 11);
        $stub->method('getValue')
             ->willReturn('7', 'Ace');

        $hand = [clone $stub, clone $stub];

        $value = $game->getHandValue($hand);
        $this->assertEquals(18, $value);
    }
}
