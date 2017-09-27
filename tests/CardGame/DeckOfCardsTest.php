<?php

namespace MauMau\CardGame;

use PHPUnit\Framework\TestCase;

class DeckOfCardsTest extends TestCase
{
    const JOKERS = 2;

    protected $normalDeck;
    protected $emptyDeck;
    protected $rules;

    public function setUp()
    {
        $this->rules = new MauMau\Rules();
        $this->normalDeck = new DeckOfCards($this->rules);
        $this->normalDeck->init();

        $this->emptyDeck = new DeckOfCards($this->rules);
    }

    public function testInit()
    {
        $this->assertInstanceOf(DeckOfCards::class, $this->normalDeck);
        $this->assertFalse($this->normalDeck->isEmpty());
    }

    public function testInitWithJokers()
    {
        $rules = $this->createPartialMock(MauMau\Rules::class, ['jokersAllowed']);
        $rules->expects($this->any())->method('jokersAllowed')->willReturn(self::JOKERS);

        $deckWithJokers = new DeckOfCards($rules);
        $deckWithJokers->init();

        $this->assertEquals($deckWithJokers->count() - $this->normalDeck->count(), self::JOKERS);
    }

    public function getSampleCards()
    {
        return [
            new Card('hearts', '2', $this->rules),
            new Card('spades', 'Q', $this->rules),
            new Card('hearts', '8', $this->rules),
            new Card('clubs', '7', $this->rules)
        ];
    }

    public function testAddCardOnBottomAndPopulate()
    {
        $this->emptyDeck->populate($this->normalDeck);
        $this->assertEquals($this->emptyDeck->getCards(), $this->normalDeck->getCards());
    }

    public function testShuffle()
    {
        $cards = $this->normalDeck->getCards();
        $this->normalDeck->shuffle();
        $this->assertNotEquals($cards, $this->normalDeck->getCards());
    }

    public function testAddAndDrawCardFromTop()
    {
        $cards = $this->getSampleCards();
        $this->normalDeck->addCardOnTop($cards[0]);
        $topCard = $this->normalDeck->drawCardFromTop();

        $this->assertInstanceOf(Card::class, $topCard);
        $this->assertSame($topCard, $cards[0]);
    }

    public function testAddAndDrawCardFromBottom()
    {
        $cards = $this->getSampleCards();
        $this->normalDeck->addCardOnBottom($cards[0]);
        $bottomCard = $this->normalDeck->drawCardFromBottom();

        $this->assertInstanceOf(Card::class, $bottomCard);
        $this->assertSame($bottomCard, $cards[0]);
    }

    public function testDrawRandomCard()
    {
        $card = $this->normalDeck->drawRandomCard();
        $this->assertInstanceOf(Card::class, $card);
    }

    public function testRemoveExistingCard()
    {
        $deck = new DeckOfCards($this->rules);
        $cards = $this->getSampleCards();

        foreach ($cards as $card) {
            $deck->addCardOnBottom($card);
        }

        $count = $deck->count();

        try {
            $deck->removeCard($cards[0]);
        } catch (\Exception $e) {
            self::fail('Failed removing existing card');
        }

        $this->assertEquals($count - $deck->count(), 1);
    }

    public function testRemoveNonExistingCardThrowsException()
    {
        $deck = new DeckOfCards($this->rules);
        $this->expectException('\\Exception');
        $deck->removeCard(new Card('hearts', '2', $this->rules));
    }

    public function testDrawCardFromTopOnEmptyDeck()
    {
        $this->expectException('\\Exception');
        $this->emptyDeck->drawCardFromTop();
    }

    public function testDrawCardFromBottomOnEmptyDeck()
    {
        $this->expectException('\\Exception');
        $this->emptyDeck->drawCardFromBottom();
    }

    public function testDrawRandomCardOnEmptyDeck()
    {
        $this->expectException('\\Exception');
        $this->emptyDeck->drawRandomCard();
    }

    public function testIteration()
    {
        foreach ($this->getSampleCards() as $card) {
            $this->emptyDeck->addCardOnBottom($card);
        }

        foreach ($this->emptyDeck as $card) {
            $this->assertInstanceOf(Card::class, $card);
        }

        $this->assertEquals($this->emptyDeck->key(), $this->emptyDeck->count());
    }

    public function testToString()
    {
        $this->assertNotEmpty($this->normalDeck);
        $this->assertEquals('No cards', $this->emptyDeck);
    }
}
