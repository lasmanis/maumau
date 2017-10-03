<?php
    namespace MauMau\CardGame;

    use MauMau\Generic\DisplayInterface;
    use Psr\Log\InvalidArgumentException;

    /**
    * Abstract Player class.
    */
    abstract class AbstractPlayer implements PlayerInterface
    {
        protected $hand;
        protected $name;
        protected $rules;
        protected $strategy;
        protected $display;
        protected $game;

        /**
         * Places an appropriate card from the player's hand to the playing stack.
         *
         * @param AbstractGame $game
         * @return void
         */
        abstract public function play(AbstractGame $game);

        /**
         * Checks to see if the player is the winner.
         *
         * @return bool
         */
        abstract public function isWinner(): bool;

        /**
         * Make any necessary announcements to the world.
         *
         * @return void
         */
        abstract protected function extraAnnouncements();

        /**
         * The class constructor
         *
         * @param string $name
         * @param AbstractRules $rules
         * @param PlayerStrategyInterface $strategy
         * @param DisplayInterface $display
         * @throws InvalidArgumentException If name is empty.
         */
        public function __construct(string $name, AbstractRules $rules, PlayerStrategyInterface $strategy, DisplayInterface $display)
        {
            if (empty($name)) {
                throw new \InvalidArgumentException('Player\'s name cannot be empty.');
            }

            $this->name = $name;
            $this->rules = $rules;
            $this->strategy = $strategy;
            $this->display = $display;
        }

        /**
         * Sets the player's hand.
         *
         * @param DeckOfCards $hand
         * @return void
         */
        public function deal(DeckOfCards $hand)
        {
            $this->hand = $hand;

            $this->display->message($this->name . ' has been dealt: ' . $this->hand . "");
        }

        /**
         * Returns player's hand
         *
         * @return DeckOfCards
         */
        public function getHand(): DeckOfCards
        {
            return $this->hand;
        }

        /**
         * Returns player's name.
         *
         * @return string
         */
        public function __toString(): string
        {
            return $this->name;
        }
    }
