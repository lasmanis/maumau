<?php
    namespace MauMau\CardGame\MauMau;

    use MauMau\Generic\DisplayInterface;
    use MauMau\CardGame\DeckOfCards;
    use MauMau\CardGame\AbstractRules;
    use MauMau\CardGame\AbstractPlayer;
    use MauMau\CardGame\AbstractGame;
    use MauMau\CardGame\AbstractGameFactory;

    /**
     * GameFactory class
     */
    class GameFactory extends AbstractGameFactory
    {
        /**
         * Create a new set of rules.
         *
         * @return AbstractRules
         */
        public static function createRules(): AbstractRules
        {
            return new Rules;
        }

        /**
         * Create a new Player
         *
         * @param string $name
         * @param AbstractRules $rules
         * @param DisplayInterface $display
         * @return AbstractPlayer
         */
        public static function createPlayer(string $name, AbstractRules $rules, DisplayInterface $display): AbstractPlayer
        {
            $strategy = new PlayerStrategy($rules);
            return new Player($name, $rules, $strategy, $display);
        }

        /**
         * Create a new Game
         *
         * @param AbstractRules $rules
         * @param DeckOfCards $deck
         * @param DisplayInterface $display
         * @return AbstractGame
         */
        public static function createGame(AbstractRules $rules, DeckOfCards $deck, DisplayInterface $display): AbstractGame
        {
            return new Game($rules, $deck, $display);
        }

    }