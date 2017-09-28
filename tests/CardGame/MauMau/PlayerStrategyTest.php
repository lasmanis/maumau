<?php

namespace MauMau\CardGame\MauMau;

use MauMau\CardGame\Card;
use MauMau\CardGame\DeckOfCards;
use PHPUnit\Framework\TestCase;

class PlayerStrategyTest extends TestCase
{
    protected $strategy;
    protected $rules;

    public function setUp()
    {
        $this->rules = new Rules();
        $this->strategy = new PlayerStrategy($this->rules);
    }

    public function testPickCardBasedOnSuit()
    {
        $selectedCards = $this->strategy->pickCard($this->playableCardsBasedOn3OfHearts(), $this->playerHand());
        $this->assertEquals(1, $selectedCards->count());

        $selectedCard = $selectedCards->drawCardFromTop();
        $this->assertEquals('hearts', $selectedCard->getSuit());
        $this->assertEquals('7', $selectedCard->getName());
    }

    public function testPickCardBasedOnName()
    {
        $selectedCards = $this->strategy->pickCard($this->playableCardsBasedOn3OfDiamonds(), $this->playerHand());
        $this->assertEquals(1, $selectedCards->count());

        $selectedCard = $selectedCards->drawCardFromTop();
        $this->assertEquals('clubs', $selectedCard->getSuit());
        $this->assertEquals('3', $selectedCard->getName());
    }

    public function playerHand(): DeckOfCards
    {
        $deck = new DeckOfCards($this->rules);
        $deck->populate([
            new Card('hearts', 'J', $this->rules),
            new Card('hearts', '8', $this->rules),
            new Card('hearts', '7', $this->rules),
            new Card('diamonds', 'Q', $this->rules),
            new Card('spades', '3', $this->rules),
            new Card('clubs', '3', $this->rules)
        ]);

        return $deck;
    }

    /**
     * Let's assume that the Top Card is 3 of Hearts
     *
     * @return DeckOfCards
     */
    public function playableCardsBasedOn3OfHearts(): DeckOfCards
    {
        $deck = new DeckOfCards($this->rules);
        $deck->populate([
            new Card('hearts', 'J', $this->rules),
            new Card('hearts', '8', $this->rules),
            new Card('hearts', '7', $this->rules),
            new Card('spades', '3', $this->rules),
            new Card('clubs', '3', $this->rules)
        ]);

        return $deck;
    }

    /**
     * Let's assume that the Top Card is 3 of Diamonds
     *
     * @return DeckOfCards
     */
    public function playableCardsBasedOn3OfDiamonds(): DeckOfCards
    {
        $deck = new DeckOfCards($this->rules);
        $deck->populate([
            new Card('diamonds', 'Q', $this->rules),
            new Card('spades', '3', $this->rules),
            new Card('clubs', '3', $this->rules)
        ]);

        return $deck;
    }
}
