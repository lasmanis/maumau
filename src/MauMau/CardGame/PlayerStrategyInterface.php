<?php
    namespace MauMau\CardGame;

	/**
    * PlayerStrategy interface
    */
    interface PlayerStrategyInterface
    {
        public function __construct(AbstractRules $rules);
        public function pickCard(DeckOfCards $playableCards, DeckOfCards $playerHand): DeckOfCards;
    }
