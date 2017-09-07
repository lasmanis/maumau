<?php
    namespace MauMau;

    /**
    * Game class.
    */
    class Game
    {
        private $players;
        private $rules;
        private $drawingStack;
        private $playingStack;
        private $activePlayerIndex;
        private $round = 0;

        public function __construct(Rules $rules, DeckOfCards $deck)
        {
            $this->rules = $rules;
            $this->drawingStack = $deck;

            echo 'Starting new game...' . PHP_EOL;
        }

        /**
         * Adds a player to the players array.
         *
         * @param Player $player
         * @throws Exception If max number of players is reached.
         * @return void
         */
        public function join(Player $player)
        {
            if (count($this->players) < $this->rules::MAX_PLAYERS) {
                $this->players[] = $player;
                return;
            }

            throw new \Exception('Maximum number of players reached.');
        }

        /**
         * Starts the game
         *
         * @throws Exception if there are not enough players
         * @return void
         */
        public function start()
        {
            if (!$this->rules->validateNumberOfPlayers(count($this->players))) {
                throw new \Exception('Not enough players');
            }

            $this->deal();
            $this->setPlayingStack();
            $this->pickFirstPlayer();

            $this->gameLoop();
        }

        /**
         * Deals a hand of cards to each player, based on the rules.
         *
         * @throws Exception If there are not enough cards left.
         * @return void
         */
        protected function deal()
        {
            echo "Deal has started...\n";
            foreach ($this->players as $player) {
                $hand = new DeckOfCards($this->rules);

                for ($i = 0; $i < $this->rules::HAND_SIZE; $i++) {
                    try {
                        $hand->addCardOnTop($this->drawingStack->drawCardFromTop());
                    } catch (Exception $e) {
                        throw new Exception("Could not deal for player " . $player . ". " . $e->getMessage());
                    }
                }

                $player->deal($hand);
            }
        }

        /**
         * Sets the playing stack. Where players will play their cards.
         *
         * @throws Exception if there are not enough cards left.
         * @return void
         */
        protected function setPlayingStack()
        {
            $this->playingStack = new DeckOfCards($this->rules);
            try {
                $topCard = $this->drawingStack->drawCardFromTop();
            } catch (Exception $e) {
                throw new Exception("Not enough cards left.");
            }

            $this->playingStack->addCardOnTop($topCard);

            echo "Top card is: $topCard\n";
        }

        /**
         * Picks the player that will start the game.
         *
         * @return void
         */
        protected function pickFirstPlayer()
        {
            $this->activePlayerIndex = array_rand($this->players);
            $player = $this->players[$this->activePlayerIndex];

            echo "$player was selected to start first!\n";
        }

        /**
         * Handles the core logic of the game and runs the game until it's finished.
         *
         * @return void
         */
        protected function gameLoop()
        {
            $reshuffles = 0;
            $safety = 100;
            $rounds = 1;
            $plays = 0;
            $players = count($this->players);
            while (!$this->weHaveAWinner() && $rounds < $safety) {
                if ($this->drawingStack->isEmpty()) {
                    $this->reshuffleDecks();
                    $reshuffles++;
                }

                $this->players[$this->activePlayerIndex]->play($this->playingStack, $this->drawingStack);
                $this->updatePlayerTurn();

                $this->checkCheats();

                usleep(25000);

                // Some basic stats
                $plays++;
                if ($plays % $players === 0){
                    $rounds++;
                }
            }

            if (!$this->weHaveAWinner()) {
                echo "This is taking too long. Let's start a new game!\n";
            } else {
                echo "Game concluded after $rounds rounds\n";
            }

            if ($reshuffles > 0) {
                echo $reshuffles . ' reshuffle' . ($reshuffles === 1 ? '' : 's') . " necessary\n";
            }
        }

        /**
         * Checks to see if there is a winner
         *
         * @return type
         */
        protected function weHaveAWinner(): bool
        {
            foreach ($this->players as $player) {
                if ($player->isWinner()) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Moves all but the top card of the playing stack to the drawing stack, and shuffles them.
         *
         * @return void
         */
        protected function reshuffleDecks()
        {
            echo "Reshuffling the decks\n";
            // First draw the top card from the Playing Stack
            $topCard = $this->playingStack->drawCardFromTop();

            // Then populate the (empty) drawing stack with the remaining Playing stack.
            $this->drawingStack->populate($this->playingStack);
            $this->drawingStack->shuffle(); // and shuffle the cards

            // Now create a new playing stack and populate it with the top card.
            $newPlayingStack = new DeckOfCards($this->rules);
            $newPlayingStack->addCardOnTop($topCard);
            $this->playingStack = $newPlayingStack;

            echo "Playing stack now has " . count($this->playingStack) . " cards.\n";
            echo "Drawing stack now has " . count($this->drawingStack) . " cards.\n";
        }

        /**
         * Update player turn.
         *
         * @return void
         */
        protected function updatePlayerTurn()
        {
            if ($this->rules::PLAY === 'CLOCKWISE') {
                $this->activePlayerIndex = $this->activePlayerIndex > 0 ? $this->activePlayerIndex - 1 : count($this->players) - 1;
            } else {
                $this->activePlayerIndex = $this->activePlayerIndex < count($this->players) - 1 ? $this->activePlayerIndex + 1 : 0;
            }
        }

        protected function checkCheats()
        {
            $totalCards = count($this->playingStack) + count($this->drawingStack);
            foreach ($this->players as $player) {
                $totalCards += count($player->getHand());
            }

            if ($totalCards !== $this->rules->deckSize()){
                throw new Exception("Someone is cheating!!");
            }
        }
    }
