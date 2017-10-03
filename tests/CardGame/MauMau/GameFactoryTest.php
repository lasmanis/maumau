<?php

namespace MauMau\CardGame\MauMau;

use MauMau\CardGame\AbstractPlayer;
use MauMau\CardGame\AbstractRules;
use MauMau\CardGame\AbstractGame;
use MauMau\CardGame\DeckOfCards;
use MauMau\Generic\BrowserDisplay;
use MauMau\Generic\CLIDisplay;
use MauMau\Generic\DisplayInterface;
use PHPUnit\Framework\TestCase;

class GameFactoryTest extends TestCase
{
    public function testCreateDeck()
    {
        $rules = $this->createMock(AbstractRules::class);
        $deck = $this->createMock(DeckOfCards::class);
        $deck->expects($this->once())->method('init');
        $deck->expects($this->once())->method('shuffle');

        $deck = GameFactory::createDeck($rules, $deck);
        $this->assertInstanceOf(DeckOfCards::class, $deck);
    }

    public function testCreateDisplay()
    {
        $display = GameFactory::createDisplay(GameFactory::FORCE_NONE);
        $this->assertInstanceOf(CLIDisplay::class, $display);

        $display = GameFactory::createDisplay(GameFactory::FORCE_BROWSER);
        $this->assertInstanceOf(BrowserDisplay::class, $display);

        $display = GameFactory::createDisplay(GameFactory::FORCE_CLI);
        $this->assertInstanceOf(CLIDisplay::class, $display);
    }

    public function testCreateRules()
    {
        $rules = GameFactory::createRules();
        $this->assertInstanceOf(AbstractRules::class, $rules);
    }

    public function testCreatePlayer()
    {
        $player = GameFactory::createPlayer(
            'Eve',
            $this->createMock(AbstractRules::class),
            $this->createMock(DisplayInterface::class)
        );

        $this->assertInstanceOf(AbstractPlayer::class, $player);
        $this->assertEquals('Eve', (string)$player);
    }

    public function testCreateGame()
    {
        $game = GameFactory::createGame(
            $this->createMock(AbstractRules::class),
            $this->createMock(DeckOfCards::class),
            $this->createMock(DisplayInterface::class)
        );

        $this->assertInstanceOf(AbstractGame::class, $game);
    }
}
