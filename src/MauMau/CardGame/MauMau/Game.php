<?php
    namespace MauMau\CardGame\MauMau;

    use MauMau\CardGame\AbstractGame;
    use MauMau\CardGame\DeckOfCards;
    use MauMau\CardGame\PlayerInterface;

    /**
    * Game class.
    */
    class Game extends AbstractGame
    {
        private $rounds = 0;
        private $plays = 0;
        private $reshuffles = 0;
        private $safety = 100;

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
         * Perform any environment initializations needed.
         *
         * @return void
         */
        protected function initEnvironment()
        {
            $this->deal();
            $this->setPlayingStack();
            $this->pickFirstPlayer();
        }

        /**
         * Deals a hand of cards to each player, based on the rules.
         *
         * @throws Exception If there are not enough cards left.
         * @return void
         */
        protected function deal()
        {
            $this->display->message("Deal has started...");
            foreach ($this->players as $player) {
                $hand = new DeckOfCards($this->rules);

                for ($i = 0; $i < $this->rules->getHandSize(); $i++) {
                    try {
                        $hand->addCardOnTop($this->drawingStack->drawCardFromTop());
                    } catch (\Exception $e) {
                        throw new \Exception("Could not deal for player " . $player . ". " . $e->getMessage());
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
            } catch (\Exception $e) {
                throw new \Exception("Not enough cards left.");
            }

            $this->playingStack->addCardOnTop($topCard);

            $this->display->message("Top card is: $topCard");
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

            $this->display->message("$player was selected to start first!");
        }

        /**
         * Allows the concrete game to perform any logic upon the start of a turn.
         *
         * @return void
         */
        protected function turnStarted()
        {
            if ($this->drawingStack->isEmpty()) {
                $this->reshuffleDecks();
                $this->reshuffles++;
            }
        }

        /**
         * Allows the concrete game to perform any logic upon the end of a turn.
         *
         * @return void
         */
        protected function turnFinished()
        {
            $this->checkCheats();

            usleep(50000);

            $this->plays++;
            if ($this->plays % count($this->players) === 0) {
                $this->rounds++;
            }
        }

        /**
         * Allows the concrete game to perform any logic upon the start of a game.
         *
         * @return void
         */
        protected function gameStarted()
        {
            $this->display->message('Starting new game...');
        }

        /**
         * Allows the concrete game to perform any logic upon the end of a game.
         *
         * @return void
         */
        protected function gameFinished()
        {
            if (!$this->weHaveAWinner()) {
                $this->display->message("This is taking too long. Let's start a new game!");
            } else {
                $this->display->message("Game concluded after $this->rounds rounds");
            }

            if ($this->reshuffles > 0) {
                $this->display->message($this->reshuffles . ' reshuffle' . ($this->reshuffles === 1 ? '' : 's') . " necessary");
            }
        }

        /**
         * Checks if the game should continue for another turn, or it should stop.
         *
         * @return bool
         */
        protected function gameShouldContinue(): bool
        {
            return !$this->weHaveAWinner() && $this->rounds < $this->safety;
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
            $this->display->message("Reshuffling the decks");
            // First draw the top card from the Playing Stack
            $topCard = $this->playingStack->drawCardFromTop();

            // Then populate the (empty) drawing stack with the remaining Playing stack.
            $this->drawingStack->populate($this->playingStack);
            $this->drawingStack->shuffle(); // and shuffle the cards

            // Now create a new playing stack and populate it with the top card.
            $newPlayingStack = new DeckOfCards($this->rules);
            $newPlayingStack->addCardOnTop($topCard);
            $this->playingStack = $newPlayingStack;

            $this->display->message("Playing stack now has " . count($this->playingStack) . " cards.");
            $this->display->message("Drawing stack now has " . count($this->drawingStack) . " cards.");
        }

        /**
         * Updates player turn.
         *
         * @return void
         */
        protected function setNextPlayer()
        {
            if ($this->rules->getPlay() === 'clockwise') {
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

            if ($totalCards !== $this->rules->deckSize()) {
                throw new \Exception("Someone is cheating!!");
            }
        }
    }
