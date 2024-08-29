<?php

namespace App\Tests\Game;

use App\GameProj\BlackjackGameProj;
use App\Card\DeckProj;
use App\Card\Card;
use PHPUnit\Framework\TestCase;

class BlackjackGameProjEndTest extends TestCase
{
    /**
     * Test isHandBusted method.
     */
    public function testIsHandBusted()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 1, 10.00);
        $game->dealInitialCards();

        while (!$game->isHandBusted(0)) {
            $game->hitHand(0);
        }

        $this->assertTrue($game->isHandBusted(0));
    }

    /**
     * Test isGameOver method.
     */
    public function testIsGameOver()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 2, 20.00);
        $game->dealInitialCards();

        $this->assertFalse($game->isGameOver());

        $game->standHand(0);
        $game->standHand(1);

        $this->assertTrue($game->isGameOver());
    }

    /**
     * Test getCurrentHand and nextHand methods.
     */
    public function testGetCurrentHandAndNextHand()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 2, 20.00);
        $game->dealInitialCards();

        $this->assertEquals(0, $game->getCurrentHand());

        $game->nextHand();
        $this->assertEquals(1, $game->getCurrentHand());

        $game->nextHand();
        $this->assertEquals(0, $game->getCurrentHand());
    }

    /**
     * Test getCurrentHand when finished.
     */
    public function testGetCurrentHandAllHandsFinished()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 2, 20.00);
        $game->dealInitialCards();

        $handCount = 0;
        while (($currentHand = $game->getCurrentHand()) !== null) {
            $this->assertIsInt($currentHand);
            $this->assertGreaterThanOrEqual(0, $currentHand);
            $this->assertLessThan(2, $currentHand);

            $game->standHand($currentHand);
            $game->nextHand();
            $handCount++;
        }
        $this->assertEquals(2, $handCount);

        $this->assertNull($game->getCurrentHand());
    }

    /**
     * Test isComputerHand method.
     */
    public function testIsComputerHand()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 2, 20.00, 1, 'smart');

        $this->assertFalse($game->isComputerHand(0));
        $this->assertTrue($game->isComputerHand(1));
    }

    /**
     * Test checking for Blackjack.
     */
    public function testCheckForBlackjack()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 1, 10.00);

        $aceStub = $this->createMock(Card::class);
        $aceStub->method('getBlackjackValue')->willReturn(11);
        $aceStub->method('getValue')->willReturn('Ace');

        $kingStub = $this->createMock(Card::class);
        $kingStub->method('getBlackjackValue')->willReturn(10);
        $kingStub->method('getValue')->willReturn('King');

        $hand = [$aceStub, $kingStub];

        $game->setPlayerHand($hand);

        $game->checkForBlackjack();

        $hands = $game->getHands();

        $this->assertEquals('blackjack', $hands[0]['status']);
    }

    /**
     * Test finishing the game and settling bets.
     */
    public function testFinishGame()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 1, 10.00);

        while (!$game->isHandBusted(0)) {
            $game->hitHand(0);
        }

        $game->finishGame();

        $hands = $game->getHands();
        $dealerHand = $game->getDealerHand();
        $this->assertNotEquals('playing', $hands[0]['status']);
        $this->assertGreaterThanOrEqual(2, count($dealerHand));
        $this->assertEquals(990.00, $game->getPlayerBank());
    }

}
