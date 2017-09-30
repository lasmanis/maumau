<?php

namespace MauMau\CardGame\MauMau;

use MauMau\CardGame\AbstractGame;
use MauMau\CardGame\AbstractPlayer;
use MauMau\CardGame\AbstractRules;
use MauMau\CardGame\PlayerStrategyInterface;
use MauMau\CardGame\DeckOfCards;
use MauMau\CardGame\Card;
use MauMau\Generic\DisplayInterface;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{
    protected $player;
    protected $rules;
    protected $strategy;
    protected $display;
    protected $game;
    protected $drawingStack;
    protected $playingStack;

    public function setUp()
    {
        $this->rules = $this->createMock(AbstractRules::class);
        $this->rules->expects($this->any())->method('validateCard')->willReturn(true);

        $this->strategy = $this->createMock(PlayerStrategyInterface::class);
        $this->display = $this->createMock(DisplayInterface::class);

        $this->game = $this->createMock(AbstractGame::class);

        $this->player = new Player('Eve', $this->rules, $this->strategy, $this->display);
    }

    public function testCreatePlayer()
    {
        $this->assertInstanceOf(AbstractPlayer::class, $this->player);
        $this->assertEquals('Eve', (string)$this->player);
    }

    public function testEmptyNameThrowsException()
    {
        $this->expectException('\InvalidArgumentException');
        $this->player = new Player('', $this->rules, $this->strategy, $this->display);
    }

    public function testDealPlayer()
    {
        $hand = $this->playerHand();

        $this->display->expects($this->once())->method('message')->with($this->player . ' has been dealt: ' . $hand . "");

        $this->player->deal($hand);

        $this->assertEquals((string)$hand, (string)$this->player->getHand());
    }

    public function testThatPlayerIsLosing()
    {
        $this->player->deal($this->playerHand());
        $this->assertFalse($this->player->isWinner());
    }

    public function testThatPlayerIsWinner()
    {
        // Deal an empty hand
        $this->player->deal(new DeckOfCards($this->rules));
        $this->assertTrue($this->player->isWinner());
    }

    public function testNormalPlay()
    {
        $this->prepareForPlay();
        $this->player->deal($this->playerHand());

        $playableCards = new DeckOfCards($this->rules);
        $playableCards ->populate([
            new Card('hearts', 'J', $this->rules),
            new Card('hearts', '8', $this->rules),
            new Card('hearts', '7', $this->rules),
            new Card('spades', '3', $this->rules),
            new Card('clubs', '3', $this->rules)
        ]);
        $this->rules->expects($this->once())->method('pickPlayableCards')->willReturn($playableCards);

        $selectedCards = new DeckOfCards($this->rules);
        $selectedCards->populate([
            new Card('hearts', '7', $this->rules)
        ]);

        $this->strategy->expects($this->once())->method('pickCard')->willReturn($selectedCards);

        $this->playingStack->expects($this->once())->method('addCardOnTop')->with(new Card('hearts', '7', $this->rules));

        $this->display->expects($this->once())->method('message')->with("$this->player plays $selectedCards");

        $this->player->play($this->game);
    }

    public function testWinningMovePlay()
    {
        $this->prepareForPlay();
        $hand = $this->emptyDeck();
        $hand->populate([
            new Card('hearts', 'J', $this->rules)
        ]);
        $this->player->deal($hand);

        $playableCards = new DeckOfCards($this->rules);
        $playableCards ->populate([
            new Card('hearts', 'J', $this->rules)
        ]);
        $this->rules->expects($this->once())->method('pickPlayableCards')->willReturn($playableCards);

        $selectedCard = new Card('hearts', 'J', $this->rules);
        $selectedCards = new DeckOfCards($this->rules);
        $selectedCards->populate([
            $selectedCard
        ]);

        $this->strategy->expects($this->once())->method('pickCard')->willReturn($selectedCards);

        $this->playingStack->expects($this->once())->method('addCardOnTop')->with($selectedCard);
        $hand->removeCard($selectedCard);

        $this->display->expects($this->at(0))->method('message')->with("$this->player plays $selectedCard");
        $this->display->expects($this->at(1))->method('message')->with("\n$this->player has won!!\n");

        $this->player->play($this->game);
    }

    public function testDrawWhenNoPlayIsPossible()
    {
        $this->prepareForPlay();
        $hand = $this->createMock(DeckOfCards::class);
        $this->player->deal($hand);

        $playableCards = $this->createMock(DeckOfCards::class);
        $this->rules->expects($this->once())->method('pickPlayableCards')->willReturn($playableCards);

        $playableCards->expects($this->any())->method('isEmpty')->willReturn(true);

        $drewCard = new Card('hearts', 'A', $this->rules);
        $this->drawingStack->expects($this->once())->method('drawCardFromTop')->willReturn($drewCard);
        $hand->expects($this->once())->method('addCardOnTop')->with($drewCard);
        $this->display->expects($this->once())->method('message')->with("$this->player does not have a suitable card, taking from deck: $drewCard");

        $this->player->play($this->game);
    }

    public function testExtraAnnouncementWhenOneCardLeft()
    {
        $this->prepareForPlay();
        $hand = $this->emptyDeck();
        $hand->populate([
            new Card('hearts', 'K', $this->rules),
            new Card('hearts', 'J', $this->rules)
        ]);
        $this->player->deal($hand);

        $playableCards = new DeckOfCards($this->rules);
        $playableCards ->populate([
            new Card('hearts', 'J', $this->rules)
        ]);
        $this->rules->expects($this->once())->method('pickPlayableCards')->willReturn($playableCards);

        $selectedCard = new Card('hearts', 'J', $this->rules);
        $selectedCards = new DeckOfCards($this->rules);
        $selectedCards->populate([
            $selectedCard
        ]);

        $this->strategy->expects($this->once())->method('pickCard')->willReturn($selectedCards);

        $this->playingStack->expects($this->once())->method('addCardOnTop')->with($selectedCard);
        $hand->removeCard($selectedCard);

        $this->display->expects($this->at(0))->method('message')->with("$this->player plays $selectedCard");
        $this->display->expects($this->at(1))->method('message')->with("$this->player has {$hand->count()} card remaining!");

        $this->player->play($this->game);
    }

    protected function playerHand(): DeckOfCards
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

    protected function emptyDeck(): DeckOfCards
    {
        return new DeckOfCards($this->rules);
    }

    protected function prepareForPlay()
    {
        $this->playingStack = $this->createMock(DeckOfCards::class);
        $this->drawingStack = $this->createMock(DeckOfCards::class);
        $this->game->expects($this->once())->method('getDrawingStack')->willReturn($this->drawingStack);
        $this->game->expects($this->once())->method('getPlayingStack')->willReturn($this->playingStack);
    }
}
