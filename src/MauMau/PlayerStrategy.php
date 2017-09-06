<?php
    namespace MauMau;

    /**
    * PlayerStrategy class
    */
    class PlayerStrategy
    {
        private $rules;

        public function __construct(Rules $rules)
        {
            $this->rules = $rules;
        }

        /**
         * Selects the best card to play.
         *
         * @param DeckOfCards $cards
         * @return Card
         */
        public function pickCard(DeckOfCards $cards): Card
        {
            return $cards->drawRandomCard();
        }
    }
