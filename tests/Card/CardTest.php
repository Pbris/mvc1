<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Dice.
 */
class CardTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties.
    */
    public function testCreateCard()
    {
        $card = new Card('Hearts', 'King');
        $this->assertInstanceOf(Card::class, $card);
        $this->assertEquals('Hearts', $card->getSuit());
        $this->assertEquals('King', $card->getValue());
    }

    /**
     * Test getBlackjackValue.
     */
    public function testGetBlackjackValue()
    {
        $cardSuit = new Card('Clubs', 'Jack');
        $cardNum = new Card('Hearts', '2');
        $cardAce = new Card('Diamonds', 'Ace');

        $this->assertEquals(10, $cardSuit->getBlackjackValue());
        $this->assertEquals(2, $cardNum->getBlackjackValue());
        $this->assertEquals(11, $cardAce->getBlackjackValue());
    }
}
