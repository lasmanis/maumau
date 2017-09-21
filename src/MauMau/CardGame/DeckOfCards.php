<?php
    namespace MauMau\CardGame;

    /**
    * DeckOfCards class.
    */
    class DeckOfCards implements \Countable, \Iterator
    {
        protected $cards = [];
        protected $rules;
        protected $position = 0;

        /**
         * The class constructor
         *
         * @param AbstractRules $rules
         * @return DeckOfCards
         */
        public function __construct(AbstractRules $rules)
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

            foreach ($this->rules->getAllowedSuits() as $suite) {
                foreach ($this->rules->getAllowedNames() as $name) {
                    $this->cards[] = new Card($suite, $name, $this->rules);
                }
            }

            if ($this->rules->jokersAllowed() > 0) {
                for ($i = 0; $i < $this->rules->jokersAllowed(); $i++) {
                    $this->cards[] = new Card('', 'Joker', $this->rules);
                }
            }
        }

        /**
         * Populates a deck based on an existing one.
         *
         * @param DeckOfCards $existingDeck
         * @return void
         */
        public function populate(DeckOfCards $existingDeck)
        {
            $this->cards = $existingDeck->getCards();
        }

        /**
         * Returns the deck's cards.
         *
         * @return array
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
         * @throws \Exception If deck is empty.
         * @return Card
         */
        public function drawCardFromTop(): Card
        {
            if ($this->isEmpty()) {
                throw new \Exception('Cannot draw card. Stack is empty.');
            }

            return array_shift($this->cards);
        }

        /**
         * Draw card from bottom of deck.
         *
         * @throws \Exception If deck is empty.
         * @return Card
         */
        public function drawCardFromBottom(): Card
        {
            if ($this->isEmpty()) {
                throw new \Exception('Cannot draw card. Stack is empty.');
            }

            return array_pop($this->cards);
        }

        /**
         * Draw a random card from the deck.
         *
         * @throws \Exception If deck is empty.
         * @return Card
         */
        public function drawRandomCard(): Card
        {
            if ($this->isEmpty()) {
                throw new \Exception('Cannot draw card. Stack is empty.');
            }

            $indexOfCard = array_rand($this->cards);
            $card = $this->cards[$indexOfCard];
            unset($this->cards[$indexOfCard]);
            $this->cards = array_values($this->cards);

            return $card;
        }

        /**
         * Removes a particular card from
         *
         * @param Card $card
         * @throws \Exception when card is not found
         * @return void
         */
        public function removeCard(Card $card)
        {
            if (($indexOfCard = array_search($card, $this->cards)) !== false) {
                unset($this->cards[$indexOfCard]);
                $this->cards = array_values($this->cards);
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
         * Returns the number of cards in the deck.
         *
         * @return int
         */
        public function count(): int
        {
            return count($this->cards);
        }

        /**
         * Sets iterator back to start.
         *
         * @return void
         */
        public function rewind()
        {
            $this->position = 0;
        }

        /**
         * Returns the current card.
         *
         * @return Card
         */
        public function current(): Card
        {
            return $this->cards[$this->position];
        }

        /**
         * Returns the current position of the iterator.
         *
         * @return int
         */
        public function key(): int
        {
            return $this->position;
        }

        /**
         * Moves the iterator forward.
         *
         * @return void
         */
        public function next()
        {
            ++$this->position;
        }

        /**
         * Checks if current position is valid.
         *
         * @return bool
         */
        public function valid(): bool
        {
            return isset($this->cards[$this->position]);
        }

        /**
         * Returns the full deck in one row.
         *
         * @return string
         */
        public function __toString(): string
        {
            return $this->isEmpty() ? 'No cards' : implode(' ', $this->cards);
        }
    }
