<?php

namespace MauMau\CardGame\MauMau;

use MauMau\CardGame\DeckOfCards;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    protected $game;
    protected $rules;
    protected $deck;
    protected $display;
    protected $strategy;

    public function setUp()
    {
        ob_start();

        // Get the Display object
        $this->display = GameFactory::createDisplay(GameFactory::FORCE_BROWSER);

        // Get the game rules
        $this->rules = GameFactory::createRules();

        // Get the Deck
        $this->deck = GameFactory::createDeck($this->rules);

        // Finally, get the game
        $this->game = GameFactory::createGame($this->rules, $this->deck, $this->display);
    }

    public function tearDown()
    {
        ob_end_clean();
    }

    /**
     * @param array $playerNames
     * @dataProvider invalidPlayersProvider
     */
    public function testInvalidNumberOfPlayersJoinThrowsException(array $playerNames)
    {
        $this->expectException(\Exception::class);

        $players = [];
        foreach ($playerNames as $name) {
            $players[] = GameFactory::createPlayer($name, $this->rules, $this->display);
        }
        $this->game->joinPlayers($players);
    }

    public function testGetDrawingStack()
    {
        $deck = $this->game->getDrawingStack();
        $this->assertInstanceOf(DeckOfCards::class, $deck);
        $this->assertEquals($deck->count(), $this->rules->deckSize());
    }

    public function testSuccessfulInitGame()
    {
        $this->initNormalGame();

        $this->assertEquals(4, $this->game->getNumberOfPlayers());
        $this->assertTrue($this->game->cardsAreDealt());

        $playingStack = $this->game->getPlayingStack();
        $this->assertInstanceOf(DeckOfCards::class, $playingStack);
        $this->assertEquals(1, $playingStack->count());
        $this->assertNotNull($this->game->getActivePlayerIndex());
        try {
            $this->game->checkCheats();
        } catch (\Exception $e) {
            $this->fail(__METHOD__ . ': checkCheats failed');
        }
    }

    public function testGameLoop()
    {
        $this->initNormalGame();

        $this->game->startGameLoop();

        $this->assertThat(true, $this->logicalOr($this->game->weHaveAWinner(), $this->game->roundLimitReached()));
    }

    public function testDeckReshuffle()
    {
        $this->initNormalGame();
        $playingStack = $this->game->getPlayingStack();
        $drawingStack = $this->game->getDrawingStack();

        while (!$drawingStack->isEmpty()) {
            $card = $drawingStack->drawCardFromTop();
            $playingStack->addCardOnTop($card);
        }

        $this->game->reshuffleDecks();

        $playingStack = $this->game->getPlayingStack();
        $drawingStack = $this->game->getDrawingStack();

        $this->assertEquals(1, $playingStack->count());
        $this->assertFalse($drawingStack->isEmpty());

        try {
            $this->game->checkCheats();
        } catch (\Exception $e) {
            $this->fail(__METHOD__ . ': checkCheats failed');
        }

        $this->game->startGameLoop();
    }

    public function initNormalGame()
    {
        $playerNames = ['Alice', 'Bob', 'Carol', 'Eve'];
        $players = [];
        foreach ($playerNames as $name) {
            $players[] = GameFactory::createPlayer($name, $this->rules, $this->display);
        }

        $this->game->joinPlayers($players);
        $this->game->init(0);
    }

    /**
     * Provides arrays of players that are always of invalid number.
     *
     * @return array
     */
    public function invalidPlayersProvider()
    {
        return [
            [[]], // no players
            [['Alice']], // too few players
            [['Alice', 'Bob', 'Carol', 'Eve', 'One too many']] // too many players
        ];
    }
}
