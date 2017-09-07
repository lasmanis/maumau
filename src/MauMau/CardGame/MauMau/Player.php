<?php
    namespace MauMau\CardGame\MauMau;

    use MauMau\CardGame\DeckOfCards;
    use MauMau\CardGame\AbstractPlayer;
    use MauMau\CardGame\AbstractGame;

    /**
    * Player class.
    */
    class Player extends AbstractPlayer
    {
        /**
         * Places an appropriate card from the player's hand to the playing stack.
         *
         * @param AbstractGame $game
         * @return void
         */
        public function play(AbstractGame $game)
        {
            $drawingStack = $game->getDrawingStack();
            $playingStack = $game->getPlayingStack();

            // Ask the Rules for the list of cards that can be played
            $playableCards = $this->rules->pickPlayableCards($playingStack, $this->hand);

            if (!$playableCards->isEmpty()) {
                // If there are cards to play, ask Strategy to pick the best card.
                $bestCards = $this->strategy->pickCard($playableCards, $this->hand);

                // Then play the card
                $message = "$this->name plays";
                foreach ($bestCards as $card) {
                    $playingStack->addCardOnTop($card);
                    $message .= ' ' . $card;
                }

                // and tell the world about it
                $this->display->message($message);
                $this->extraAnnouncements();
            } else {
                // If there are no playable cards, draw a new card and add it to the hand
                $newCard = $drawingStack->drawCardFromTop();
                $this->hand->addCardOnTop($newCard);

                $this->display->message("$this->name does not have a suitable card, taking from deck: $newCard");
            }
        }

        /**
         * Checks to see if the player is the winner.
         *
         * @return bool
         */
        public function isWinner(): bool
        {
            return $this->hand->isEmpty();
        }

        /**
         * Make any necessary announcements to the world.
         *
         * @return void
         */
        protected function extraAnnouncements()
        {
            if ($this->isWinner()) {
                $this->display->message("\n$this->name has won!!\n");
                return;
            }

            $handCount = count($this->hand);
            if ($handCount === 1) {
                $this->display->message("$this->name has $handCount card remaining!");
            }
        }
    }
