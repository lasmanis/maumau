<?php
    namespace MauMau\CardGame;

    use MauMau\CardGame\DeckOfCards;

    /**
    * Player Interface.
    */
    interface PlayerInterface
    {
        public function play(AbstractGame $game);
        public function deal(DeckOfCards $hand);
        public function isWinner(): bool;
        public function getHand(): DeckOfCards;
    }
