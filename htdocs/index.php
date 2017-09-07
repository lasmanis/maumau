<?php
    require_once('..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'autoload.php');

    // Get the Display class
    $display = new \MauMau\Generic\Display();

    // Get the game rules
    $rules = new \MauMau\CardGame\MauMau\Rules();

    // Initialize the Deck of Cards
    $deck = new \MauMau\CardGame\DeckOfCards($rules);
    try {
        $deck->init();
    } catch (Exception $e) {
        echo 'Could not initialize deck. Exiting...';
        exit;
    }
    $deck->shuffle();

    // Initialize the Game
    $game = new \MauMau\CardGame\MauMau\Game($rules, $deck, $display);

    // Setup the Players
    $strategy = new \MauMau\CardGame\MauMau\PlayerStrategy($rules);
    $playerNames = ['Alice', 'Bob', 'Carol', 'Eve'];
    foreach ($playerNames as $playerName) {
        try {
            $game->join(new \MauMau\CardGame\MauMau\Player($playerName, $rules, $strategy, $display));
            echo $playerName . ' joined.' . "\n";
        } catch (Exception $e) {
            echo $playerName . ' could not join game. Reason: ' . $e->getMessage() . "\n";
        }
    }

    // Start the Game
    try {
        $game->start();
    } catch (Exception $e) {
        echo "Failed to start the Game. Reason: " . $e->getMessage() . "\n";
    }
