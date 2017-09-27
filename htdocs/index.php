<?php
    require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

    use \MauMau\CardGame\MauMau\GameFactory;

    // Get the Display object
    $display = GameFactory::createDisplay();

    // Get the game rules
    $rules = GameFactory::createRules();

    // Get the Deck
    $deck = GameFactory::createDeck($rules);

    // Finally, get the game
    $game = GameFactory::createGame($rules, $deck, $display);

    // Get the players
    $playerNames = ['Alice', 'Bob', 'Carol', 'Eve'];
    $players = [];
    foreach ($playerNames as $name) {
        $players[] = GameFactory::createPlayer($name, $rules, $display);
    }

    // Start the Game
    try {
        $game->start($players);
    } catch (Exception $e) {
        $display->message("Failed to start the Game. Reason: " . $e->getMessage());
    }
