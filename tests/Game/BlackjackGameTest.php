<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;
use App\Deck\Deck;
use App\Deck\Card;

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
}