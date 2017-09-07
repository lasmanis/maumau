<?php
    namespace MauMau;

    /**
    * Player class.
    */
    class Player
    {
        private $hand;
        private $name;
        private $rules;
        private $strategy;
        private $display;

        public function __construct(string $name, Rules $rules, PlayerStrategy $strategy, Display $display)
        {
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
         * Places an appropriate card from the player's hand to the playing stack.
         *
         * @param DeckOfCards $playingStack
         * @param DeckOfCards $drawingStack
         * @return void
         */
        public function play(DeckOfCards $playingStack, DeckOfCards $drawingStack)
        {
            // Ask the Rules for the list of cards that can be played
            $playableCards = $this->rules->pickPlayableCards($playingStack, $this->hand);

            if (!$playableCards->isEmpty()) {
                // If there are cards to play, ask Strategy to pick the best card.
                $card = $this->strategy->pickCard($playableCards, $this->hand);

                // Then play the card
                $playingStack->addCardOnTop($card);

                // and tell the world about it
                $this->display->message("$this->name plays $card");
                $this->extraAnnouncements();
            } else {
                // If there are no playable cards, draw a new card and add it to the hand
                $newCard = $drawingStack->drawCardFromTop();
                $this->hand->addCardOnTop($newCard);

                $this->display->message("$this->name has no suitable cards to play. Drawing from deck: $newCard");
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
            $handCount = count($this->hand);
            if ($handCount === 1) {
                $this->display->message("$this->name has $handCount card".($handCount === 1 ? '' : 's')." remaining!");
            } elseif ($handCount === 0) {
                $this->display->message("\n$this->name has won!!\n");
            }
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
