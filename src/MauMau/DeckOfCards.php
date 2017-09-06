<?php
    namespace MauMau;

    /**
    * DeckOfCards class.
    */
    class DeckOfCards
    {
        private $cards = [];
        private $rules;

        public function __construct(Rules $rules)
        {
            $this->rules = $rules;
        }

        /**
         * Initializes a deck of cards using the provided Rules.
         *
         * @return void
         */
        public function init()
        {
            $this->cards = [];

            foreach ($this->rules::ALLOWED_SUITS as $suite) {
                foreach ($this->rules::ALLOWED_NAMES as $name) {
                    $this->cards[] = new Card($suite, $name, $this->rules);
                }
            }
        }

        /**
         * Populates a deck based on an existing one.
         *
         * @param DeckOfCards$existingDeck
         * @return void
         */
        public function populate(DeckOfCards $existingDeck)
        {
            $this->cards = $existingDeck->getCards();
        }

        /**
         * Returns the deck's card.
         * @return type
         */
        public function getCards(): array
        {
            return $this->cards;
        }

        /**
         * Shuffles the deck of cards.
         *
         * @return void
         */
        public function shuffle()
        {
            shuffle($this->cards);
        }

        /**
         * Adds card to the top of the deck.
         *
         * @param Card $card
         * @return void
         */
        public function addCardOnTop(Card $card)
        {
            array_unshift($this->cards, $card);
        }

        /**
         * Adds card to the bottom of the deck.
         *
         * @param Card $card
         * @return void
         */
        public function addCardOnBottom(Card $card)
        {
            $this->cards[] = $card;
        }

        /**
         * Draw card from top of deck.
         *
         * @return Card
         */
        public function drawCardFromTop(): Card
        {
            if ($this->isEmpty()){
                throw new \Exception('Cannot draw card. Stack is empty.');
            }

            return array_shift($this->cards);
        }

        /**
         * Draw card from bottom of deck.
         *
         * @return Card
         */
        public function drawCardFromBottom(): Card
        {
            if ($this->isEmpty()){
                throw new \Exception('Cannot draw card. Stack is empty.');
            }

            return array_pop($this->cards);
        }

        /**
         * Draw a random card from the deck.
         *
         * @return Card
         */
        public function drawRandomCard(): Card
        {
            if ($this->isEmpty()){
                throw new \Exception('Cannot draw card. Stack is empty.');
            }

            $indexOfCard = array_rand($this->cards);
            $card = $this->cards[$indexOfCard];
            unset($this->cards[$indexOfCard]);

            return $card;
        }

        /**
         * Removes a particular card from
         * @param Card $card
         * @return type
         */
        public function removeCard(Card $card)
        {
            if (($indexOfCard = array_search($card, $this->cards)) !== false) {
                unset($this->cards[$indexOfCard]);
                return;
            }

            throw new \Exception("Card not found");
        }

        /**
         * Checks if the deck is empty.
         *
         * @return bool
         */
        public function isEmpty(): bool
        {
            return empty($this->cards);
        }

        /**
         * Returns the full deck in one row.
         *
         * @return string
         */
        public function __toString(): string
        {
            return $this->isEmpty() ? 'No more cards' : implode(' ', $this->cards);
        }

        /**
         * Returns the number of cards in the deck.
         *
         * @return int
         */
        public function count(): int
        {
            return count($this->cards);
        }
    }
