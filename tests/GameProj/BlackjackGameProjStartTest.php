<?php

namespace App\Tests\Game;

use App\GameProj\BlackjackGameProj;
use App\Card\DeckProj;
use App\Card\Card;
use PHPUnit\Framework\TestCase;

class BlackjackGameProjStartTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties.
     */
    public function testCreateBlackjackGameProj()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 1, 10.00);
        $this->assertInstanceOf(BlackjackGameProj::class, $game);

        $this->assertEquals('Player', $game->getPlayerName());
        $this->assertEquals(990.00, $game->getPlayerBank());
        $this->assertCount(1, $game->getHands());
    }

    /**
     * Test constructor exception for invalid hands number.
     */
    public function testConstructorExceptionForInvalidNumberOfHands()
    {
        $this->expectException(\InvalidArgumentException::class);
        new BlackjackGameProj('Player', 1000.00, 4, 10.00);
    }

    /**
     * Test dealing cards.
     */
    public function testDealCards()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 1, 10.00);
        $game->dealInitialCards();

        $hands = $game->getHands();
        $this->assertCount(2, $hands[0]['hand']);

        $dealerHand = $game->getDealerHand();
        $this->assertCount(1, $dealerHand);
    }

    /**
     * Test hitting player and dealer.
     */
    public function testHit()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 1, 10.00);
        $game->dealInitialCards();

        $game->hitHand(0);
        $hands = $game->getHands();
        $this->assertCount(3, $hands[0]['hand']);

        $game->hitDealer();
        $dealerHand = $game->getDealerHand();
        $this->assertCount(2, $dealerHand);
    }

    /**
     * Test hand value, stub Card to assure the value can be asserted.
     */
    public function testGetHandValue()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 1, 10.00);

        // Create a stub for the Card class
        $stub = $this->createMock(Card::class);

        // Configure the stub
        $stub->method('getBlackjackValue')
            ->willReturn(7, 10);
        $stub->method('getValue')
            ->willReturn('7', 'King');

        $hand = [clone $stub, clone $stub];

        $value = $game->getHandValue($hand);
        $this->assertEquals(17, $value);
    }

    /**
     * Test getBustProbability method for soft ace.
     */
    public function testGetBustProbabilitySoftAce()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 1, 10.00);

        // Create stubs for the Card class
        $aceStub = $this->createMock(Card::class);
        $aceStub->method('getBlackjackValue')->willReturn(11);
        $aceStub->method('getValue')->willReturn('Ace');

        $sevenStub = $this->createMock(Card::class);
        $sevenStub->method('getBlackjackValue')->willReturn(7);
        $sevenStub->method('getValue')->willReturn('7');

        $hand = [$aceStub, $sevenStub];

        // Set the mocked hand as the player's hand
        $game->setPlayerHand($hand);

        $bustProbability = $game->getBustProbability(0);

        // With Ace-7 (soft 18), bust probability should be 0
        $this->assertEquals(0, $bustProbability);
    }

    /**
     * Test getBustProbability method.
     */
    public function testGetBustProbabilityRest()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 1, 10.00);

        // Create stubs for the Card class
        $kingStub = $this->createMock(Card::class);
        $kingStub->method('getBlackjackValue')->willReturn(10);
        $kingStub->method('getValue')->willReturn('King');

        $sevenStub = $this->createMock(Card::class);
        $sevenStub->method('getBlackjackValue')->willReturn(7);
        $sevenStub->method('getValue')->willReturn('7');

        $hand = [$kingStub, $sevenStub];

        // Set the mocked hand as the player's hand
        $game->setPlayerHand($hand);

        $bustProbability = $game->getBustProbability(0);
        $this->assertLessThan(0.70, $bustProbability);
        $this->assertGreaterThan(0.68, $bustProbability);
    }

    /**
     * Test playing the computer's hand.
     */
    public function testPlayComputerHand()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 2, 10.00, 1, 'smart');

        // Create stubs for the Card class
        $aceStub = $this->createMock(Card::class);
        $aceStub->method('getBlackjackValue')->willReturn(11);
        $aceStub->method('getValue')->willReturn('Ace');

        $sixStub = $this->createMock(Card::class);
        $sixStub->method('getBlackjackValue')->willReturn(6);
        $sixStub->method('getValue')->willReturn('6');

        $hand = [$aceStub, $sixStub];

        $game->setPlayerHand($hand, 1);

        $game->standHand(0);

        $game->playComputerHand();

        // Force a bust
        while (!$game->isHandBusted(1)) {
            $game->hitHand(1);
        }

        $hands = $game->getHands();
        $this->assertNotEquals('playing', $hands[1]['status']);
    }

    /**
     * Test standHand method.
     */
    public function testStandHand()
    {
        $game = new BlackjackGameProj('Player', 1000.00, 2, 20.00);
        $game->dealInitialCards();

        $game->standHand(0);
        $hands = $game->getHands();
        $this->assertEquals('standing', $hands[0]['status']);

        $this->expectException(\InvalidArgumentException::class);
        $game->standHand(5); // Invalid hand index
    }
}
