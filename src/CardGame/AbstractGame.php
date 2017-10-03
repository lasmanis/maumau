<?php
    namespace MauMau\CardGame;

    use MauMau\Generic\DisplayInterface;

    /**
    * Game class.
    */
    abstract class AbstractGame
    {
        protected $rules;
        protected $drawingStack;
        protected $playingStack;
        protected $display;
        protected $players;
        protected $activePlayerIndex = 0;
        protected $msTurnDelay = 50000;
        protected $rounds = 0;
        protected $safety = 100;

        /**
         * Allows the concrete game to perform any logic upon the start of a turn.
         *
         * @return void
         */
        abstract protected function turnStarted();

        /**
         * Allows the concrete game to perform any logic upon the end of a turn.
         *
         * @return void
         */
        abstract protected function turnFinished();

        /**
         * Allows the concrete game to perform any logic upon the start of a game.
         *
         * @return void
         */
        abstract protected function gameStarted();

        /**
         * Allows the concrete game to perform any logic upon the end of a game.
         *
         * @return void
         */
        abstract protected function gameFinished();

        /**
         * Updates player turn.
         *
         * @return void
         */
        abstract protected function setNextPlayer();

        /**
         * Checks if the game should continue for another turn, or it should stop.
         *
         * @return bool
         */
        abstract protected function gameShouldContinue(): bool;

        /**
         * Checks if the number of total cards in the game no longer match the original deck size.
         *
         * @throws \Exception when the condition is violated.
         * @return void
         */
        abstract public function checkCheats();

        /**
         * Checks if the cards are properly dealt.
         *
         * @return bool
         */
        abstract public function cardsAreDealt(): bool;

        /**
         * Checks if one of the players is the winner.
         *
         * @return bool
         */
        abstract public function weHaveAWinner(): bool;

        /**
         * Picks the player that will start the game.
         *
         * @return void
         */
        abstract protected function pickFirstPlayer();

        /**
         * Deals a hand of cards to each player, based on the rules.
         *
         * @throws \Exception If there are not enough cards left.
         * @return void
         */
        abstract protected function deal();

        /**
         * Makes sure to reshuffle the decks accordingly in case it is deemed necessary throughout the game.
         *
         * @return void
         */
        abstract public function reshuffleDecks();

        /**
         * The class constructor.
         *
         * @param AbstractRules $rules
         * @param DeckOfCards $deck
         * @param DisplayInterface $display
         */
        public function __construct(AbstractRules $rules, DeckOfCards $deck, DisplayInterface $display)
        {
            $this->rules = $rules;
            $this->drawingStack = $deck;
            $this->display = $display;

            $this->gameStarted();
        }

        /**
         * Adds a player to the players array.
         *
         * @param PlayerInterface $player
         * @throws \Exception If max number of players is reached.
         * @return void
         */
        protected function join(PlayerInterface $player)
        {
            if (count($this->players) < $this->rules->getMaxPlayers()) {
                $this->players[] = $player;
                return;
            }

            throw new \Exception('Maximum number of players reached.');
        }

        /**
         * Return the drawing stack of cards
         *
         * @return DeckOfCards
         */
        public function getDrawingStack(): DeckOfCards
        {
            return $this->drawingStack;
        }

        /**
         * Return the playing stack of cards
         *
         * @return DeckOfCards
         */
        public function getPlayingStack(): DeckOfCards
        {
            return $this->playingStack;
        }

        /**
         * Return the number of players that joined the game.
         *
         * @return int
         */
        public function getNumberOfPlayers(): int
        {
            return is_array($this->players) ? count($this->players) : 0;
        }

        /**
         * Return the array index of the active player; ie. the player whose turn is to play.
         *
         * @return int
         */
        public function getActivePlayerIndex(): int
        {
            return $this->activePlayerIndex;
        }

        /**
         * Handles the core logic of the game and runs the game until it's finished.
         *
         * @return void
         */
        final public function startGameLoop()
        {
            while ($this->gameShouldContinue()) {
                $this->turnStarted();

                $this->players[$this->activePlayerIndex]->play($this);

                $this->setNextPlayer();

                $this->turnFinished();
            }

            $this->gameFinished();
        }

        /**
         * Joins a group of players to the game.
         *
         * @param array $players
         * @throws \Exception if the number of players is invalid.
         */
        final public function joinPlayers(array $players)
        {
            foreach ($players as $player) {
                $this->join($player);
                $this->display->message($player . ' joined.');
            }

            if (!$this->rules->validateNumberOfPlayers(count($this->players))) {
                throw new \Exception('Not enough players');
            }
        }

        /**
         * Initializes the game environment.
         *
         * @param int $msTurnDelay
         * @return void
         */
        public function init(int $msTurnDelay = null)
        {
            if (!is_null($msTurnDelay)) {
                $this->msTurnDelay = $msTurnDelay;
            }

            $this->deal();
            $this->pickFirstPlayer();
        }

        /**
         * Checks if the safety limit for the maximum number of allowed rounds has been reached.
         *
         * @return bool
         */
        public function roundLimitReached(): bool
        {
            return $this->rounds >= $this->safety;
        }
    }
