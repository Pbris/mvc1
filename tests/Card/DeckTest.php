<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Deck.
 */
class DeckTest extends TestCase
{
    /**
     * Construct object and verify that the deck is created with correct number of cards.
     */
    public function testCreateDeck()
    {
        $deck = new Deck();
        $this->assertInstanceOf(Deck::class, $deck);
        $this->assertEquals(52, $deck->remainingCardsCount());
    }

    /**
     * Test DrawCard.
     */
    public function testDrawCard()
    {
        $deck = new Deck();
        $card = $deck->drawCard();

        $this->assertInstanceOf(CardGraphic::class, $card);
        $this->assertEquals(51, $deck->remainingCardsCount());
    }

    /**
     * Test GetCards.
     */
    public function testGetCards()
    {
        $deck = new Deck();
        $cards = $deck->getCards();
        
        // Check 4 suits
        $this->assertCount(4, $cards); 
        
        foreach ($cards as $suit => $cardsInSuit) {
            $this->assertCount(13, $cardsInSuit);
        }
    }

    /**
     * Test draw all cards.
     */
    public function testDrawAllCards()
    {
        $deck = new Deck();
        
        for ($i = 0; $i < 52; $i++) {
            $deck->drawCard();
        }
        
        $this->assertEquals(0, $deck->remainingCardsCount());
        $this->assertNull($deck->drawCard());
    }
}
