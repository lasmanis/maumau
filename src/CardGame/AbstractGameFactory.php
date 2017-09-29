<?php
    namespace MauMau\CardGame;

    use MauMau\Generic\DisplayInterface;
    use MauMau\Generic\BrowserDisplay;
    use MauMau\Generic\CLIDisplay;
    use Psr\Log\InvalidArgumentException;

    /**
     * AbstractGameFactory class
     */
    abstract class AbstractGameFactory
    {
        const FORCE_NONE = 0;
        const FORCE_BROWSER = 1;
        const FORCE_CLI = 2;

        /**
         * Create a concrete Display object.
         *
         * @param int $force
         * @return DisplayInterface
         */
        public static function createDisplay(int $force = self::FORCE_NONE): DisplayInterface
        {
            if ($force === self::FORCE_BROWSER) {
                return new BrowserDisplay();
            }

            if ($force === self::FORCE_CLI || php_sapi_name() === "cli") {
                return new CLIDisplay();
            }

            return new BrowserDisplay();
        }

        /**
         * Create and initialize DeckOfCards
         *
         * @param AbstractRules $rules
         * @param DeckOfCards|null $deck
         * @return DeckOfCards
         */
        public static function createDeck(AbstractRules $rules, DeckOfCards $deck = null): DeckOfCards
        {
            $deck = !is_null($deck) ? $deck : new DeckOfCards($rules);
            $deck->init();
            $deck->shuffle();

            return $deck;
        }

        /**
         * Create a new set of rules.
         *
         * @return AbstractRules
         */
        abstract public static function createRules(): AbstractRules;

        /**
         * Create a new Player
         *
         * @param string $name
         * @param AbstractRules $rules
         * @param DisplayInterface $display
         * @throws InvalidArgumentException if name is empty
         * @return AbstractPlayer
         */
        abstract public static function createPlayer(string $name, AbstractRules $rules, DisplayInterface $display): AbstractPlayer;

        /**
         * Create a new Game
         *
         * @param AbstractRules $rules
         * @param DeckOfCards $deck
         * @param DisplayInterface $display
         * @return AbstractGame
         */
        abstract public static function createGame(AbstractRules $rules, DeckOfCards $deck, DisplayInterface $display): AbstractGame;
    }
