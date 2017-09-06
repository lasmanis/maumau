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

        public function __construct(string $name, Rules $rules, PlayerStrategy $strategy)
        {
            $this->name = $name;
            $this->rules = $rules;
            $this->strategy = $strategy;
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

            echo $this->name . ' has been dealt: ' . $this->hand . "\n";
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
                $card = $this->strategy->pickCard($playableCards);

                // Then play the card
                $this->hand->removeCard($card);
                $playingStack->addCardOnTop($card);

                // and tell the world about it
                echo "$this->name plays $card.\n";
                $this->extraAnnouncements();
            } else {
                // If there are no playable cards, draw a new card and add it to the hand
                $newCard = $drawingStack->drawCardFromTop();
                $this->hand->addCardOnTop($newCard);

                echo "$this->name has no suitable cards to play. Drawing from deck: $newCard\n";
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
            $handCount = $this->hand->count();
            if ($handCount === 1) {
                echo "$this->name has $handCount card".($handCount === 1 ? '' : 's')." remaining!\n";
            } elseif ($handCount === 0) {
                echo "$this->name has won!!\n";
            }
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
