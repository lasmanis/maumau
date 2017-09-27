<?php

namespace MauMau\CardGame\MauMau;

use MauMau\CardGame\DeckOfCards;
use PHPUnit\Framework\TestCase;
use MauMau\CardGame\Card;

class RulesTest extends TestCase
{
    protected $rules;

    public function setUp()
    {
        $this->rules = new Rules();
    }

    public function testDeckSize()
    {
        $this->assertTrue($this->rules->deckSize() > 0);
    }

    public function getCardSamples()
    {
        $rules = new Rules();
        return [
            [11, new Card('hearts', 'J', $rules)],
            [0, new Card('any', 'Joker', $rules)],
            [2, new Card('clubs', '2', $rules)]
        ];
    }

    public function getCardPairs()
    {
        $rules = new Rules();
        return [
            [true, new Card('hearts', 'J', $rules), new Card('hearts', '2', $rules)],
            [true, new Card('spades', 'A', $rules), new Card('hearts', 'A', $rules)],
            [true, new Card('any', 'Joker', $rules), new Card('hearts', 'A', $rules)],
            [true, new Card('any', 'Joker', $rules), new Card('any', 'Joker', $rules)],
            [false, new Card('spades', 'Q', $rules), new Card('diamonds', '5', $rules)]
        ];
    }

    public function validateCardSamples()
    {
        $rules = new Rules();
        return [
            [true, 'hearts', 'J'],
            [true, 'any', 'Joker'],
            [false, 'any', '2'],
            [false, 'clubs', 'any']
        ];
    }

    /**
     * @param int $expected
     * @param Card $card
     * @dataProvider getCardSamples
     */
    public function testCardValue(int $expected, Card $card)
    {
        $this->assertEquals($expected, $this->rules->cardValue($card));
    }

    /**
     * @param bool $expected
     * @param Card $cardA
     * @param Card $cardB
     * @dataProvider getCardPairs
     */
    public function testCardMatch(bool $expected, Card $cardA, Card $cardB)
    {
        $this->assertEquals($expected, $this->rules->cardsMatch($cardA, $cardB));
    }

    private function playerHandMatch()
    {
        return [
            new Card('any', 'Joker', $this->rules),
            new Card('spades', '5', $this->rules),
            new Card('spades', '7', $this->rules),
            new Card('clubs', 'Q', $this->rules),
            new Card('hearts', 'A', $this->rules),
            new Card('hearts', '10', $this->rules)
        ];
    }

    private function playerHandNoMatch()
    {
        return [
            new Card('spades', '5', $this->rules),
            new Card('spades', '7', $this->rules),
            new Card('clubs', 'Q', $this->rules),
            new Card('hearts', 'A', $this->rules),
            new Card('hearts', '10', $this->rules)
        ];
    }

    public function testPickPlayableCards()
    {
        $playingStack = new DeckOfCards($this->rules);
        $playingStack->addCardOnTop(new Card('hearts', '7', $this->rules));

        $playerHand = new DeckOfCards($this->rules);
        $playerHand->populate($this->playerHandMatch());

        $matches = $this->rules->pickPlayableCards($playingStack, $playerHand);
        $this->assertEquals(4, $matches->count());

        $playingStack->drawCardFromTop();
        $playingStack->addCardOnTop(new Card('diamonds', '6', $this->rules));

        $playerHand->clear();
        $playerHand->populate($this->playerHandNoMatch());
        $matches = $this->rules->pickPlayableCards($playingStack, $playerHand);
        $this->assertTrue($matches->isEmpty());
    }

    /**
     * @param bool $expected
     * @param string $suit
     * @param string $name
     * @dataProvider validateCardSamples
     */
    public function testValidateCard(bool $expected, string $suit, string $name)
    {
        $this->assertEquals($expected, $this->rules->validateCard($suit, $name));
    }

    public function testValidateNumberOfPlayers()
    {
        $this->assertTrue($this->rules->validateNumberOfPlayers(3));
        $this->assertFalse($this->rules->validateNumberOfPlayers(1));
        $this->assertFalse($this->rules->validateNumberOfPlayers(8));
    }

    public function testConfiguration()
    {
        $minPlayers = $this->rules->getMinPlayers();
        $maxPlayers = $this->rules->getMaxPlayers();
        $handSize = $this->rules->getHandSize();
        $play = $this->rules->getPlay();

        $this->assertInternalType('int', $minPlayers);
        $this->assertGreaterThan(0, $minPlayers);

        $this->assertInternalType('int', $maxPlayers);
        $this->assertGreaterThan(0, $maxPlayers);
        $this->assertGreaterThan($minPlayers, $maxPlayers);

        $this->assertInternalType('int', $handSize);
        $this->assertGreaterThan(0, $handSize);

        $this->assertInternalType('string', $play);
        $this->assertStringEndsWith('wise', $play);
    }
}
