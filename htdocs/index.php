<?php
    require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

    // Get the Display object
    $display = \MauMau\CardGame\MauMau\GameFactory::createDisplay();

    // Get the game rules
    $rules = \MauMau\CardGame\MauMau\GameFactory::createRules();

    // Get the Deck
    $deck = \MauMau\CardGame\MauMau\GameFactory::createDeck($rules);

    // Finally, get the game
    $game = \MauMau\CardGame\MauMau\GameFactory::createGame($rules, $deck, $display);

    // Get the players
    $playerNames = ['Alice', 'Bob', 'Carol', 'Eve'];
    $players = [];
    foreach ($playerNames as $name) {
        $players[] = \MauMau\CardGame\MauMau\GameFactory::createPlayer($name, $rules, $display);
    }

    // Start the Game
    try {
        $game->start($players);
    } catch (Exception $e) {
        $display->message("Failed to start the Game. Reason: " . $e->getMessage());
    }
