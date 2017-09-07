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

        /**
         * Perform any environment initializations needed.
         *
         * @return void
         */
        abstract protected function initEnvironment();

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
         * The class constructor.
         *
         * @param AbstractRules $rules
         * @param DeckOfCards $deck
         * @param DisplayInterface $display
         * @return AbstractGame
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
         * @param Player $player
         * @throws Exception If max number of players is reached.
         * @return void
         */
        public function join(PlayerInterface $player)
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
         * Handles the core logic of the game and runs the game until it's finished.
         *
         * @return void
         */
        final protected function startGameLoop()
        {
            $players = count($this->players);
            while ($this->gameShouldContinue()) {
                $this->turnStarted();

                $this->players[$this->activePlayerIndex]->play($this);

                $this->setNextPlayer();

                $this->turnFinished();
            }

            $this->gameFinished();
        }

        /**
         * Starts the game
         *
         * @throws Exception if there are not enough players
         * @return void
         */
        final public function start(array $players)
        {
            foreach ($players as $player) {
                try {
                    $this->join($player);
                    $this->display->message($player . ' joined.');
                } catch (\Exception $e) {
                    $this->display->message($player . ' could not join game. Reason: ' . $e->getMessage());
                }
            }

            if (!$this->rules->validateNumberOfPlayers(count($this->players))) {
                throw new \Exception('Not enough players');
            }

            $this->initEnvironment();
            $this->startGameLoop();
        }
    }
