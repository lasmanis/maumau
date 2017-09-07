<?php
    namespace MauMau\CardGame;

    /**
    * AbstractRules class.
    */
    abstract class AbstractRules
    {
        protected $allowedSuits = ['hearts', 'diamonds', 'spades', 'clubs'];
        protected $allowedNames = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
        protected $minPlayers = 2;
        protected $maxPlayers = 4;
        protected $handSize = 7;
        protected $play = 'clockwise';
        protected $jockers = 2;

        /**
         * Gets the proper card value based on the name.
         *
         * @param Card $card
         * @return int
         */
        abstract public function cardValue(Card $card): int;

        /**
         * Return the deck size.
         *
         * @return int
         */
        abstract public function deckSize(): int;

        /**
         * Picks all the playable cards based on the current playing stack
         * and the players hand, and, of course, the rules.
         *
         * @param DeckOfCards $playingStack
         * @param DeckOfCards $playerHand
         * @return DeckOfCards
         */
        abstract public function pickPlayableCards(DeckOfCards $playingStack, DeckOfCards $playerHand): DeckOfCards;

        /**
         * Checks if a player's card is a match and can be played.
         *
         * @param Card $cardToTest
         * @param Card $cardToCompare
         * @return bool
         */
        abstract public function cardsMatch(Card $cardToTest, Card $cardToCompare): bool;

        /**
         * Validates a card suit and name.
         *
         * @param string $suit
         * @param string $name
         * @return bool
         */
        public function validateCard(string $suit, string $name): bool
        {
            if ($name === 'Joker') {
                return true;
            }

            if (!in_array($suit, $this->allowedSuits)) {
                return false;
            }

            if (!in_array($name, $this->allowedNames)) {
                return false;
            }

            return true;
        }

        /**
         * Checks if the provided number of players is allowed.
         *
         * @param int $players
         * @return bool
         */
        public function validateNumberOfPlayers(int $players): bool
        {
            return $players >= $this->minPlayers &&
                $players <= $this->maxPlayers &&
                ($players * $this->handSize < $this->deckSize());
        }

        /**
         * Gets the proper card color based on the suit.
         *
         * @param Card $card
         * @return string
         */
        public function cardColor(Card $card): string
        {
            if ($card->isJoker()) {
                return 'any';
            }

            $color = '';
            switch ($card->getSuit()) {
                case 'hearts':
                case 'diamonds':
                    $color = 'red';
                    break;

                case 'clubs':
                case 'spades':
                    $color = 'black';
                    break;
            }

            return $color;
        }

        /**
         * Return allowed suits array.
         *
         * @return array
         */
        public function getAllowedSuits(): array
        {
            return $this->allowedSuits;
        }

        /**
         * Return allowed names array.
         *
         * @return array
         */
        public function getAllowedNames(): array
        {
            return $this->allowedNames;
        }

        /**
         * Return min allowed players.
         *
         * @return int
         */
        public function getMinPlayers(): int
        {
            return $this->minPlayers;
        }

        /**
         * Return max allowed players.
         *
         * @return int
         */
        public function getMaxPlayers(): int
        {
            return $this->maxPlayers;
        }

        /**
         * Return hand size.
         *
         * @return int
         */
        public function getHandSize(): int
        {
            return $this->handSize;
        }

        /**
         * Return play order.
         *
         * @return string
         */
        public function getPlay(): string
        {
            return $this->play;
        }

        /**
         * Returns allowed number of jokers.
         *
         * @return int
         */
        public function jokersAllowed(): int
        {
            return $this->jokers;
        }
    }
