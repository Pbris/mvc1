<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class CardGraphic.
 */
class CardGraphicTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties.
     */
    public function testCreateCardGraphic()
    {
        $cardGraphic = new CardGraphic('Hearts', 'King');
        $this->assertInstanceOf(CardGraphic::class, $cardGraphic);
        $this->assertEquals('Hearts', $cardGraphic->getSuit());
        $this->assertEquals('King', $cardGraphic->getValue());
    }

    /**
     * Test getAsString.
     */
    public function testGetAsString()
    {
        $cardHearts = new CardGraphic('Hearts', '10');
        $cardSpades = new CardGraphic('Spades', 'Ace');

        $this->assertEquals('♥10', $cardHearts->getAsString());
        $this->assertEquals('♠Ace', $cardSpades->getAsString());
    }
}
