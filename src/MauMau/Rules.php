<?php
    namespace MauMau;

    /**
    * Rules class.
    */
    class Rules
    {
        private $allowedSuits = ['hearts', 'diamonds', 'spades', 'clubs'];
        private $allowedNames = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
        private $minPlayers = 2;
        private $maxPlayers = 4;
        private $handSize = 7;
        private $play = 'clockwise';

        private $allowedMatches = ['suit', 'value'];

        public function __construct()
        {
        }

        /**
         * Validates a card suit and name.
         *
         * @param string $suit
         * @param string $name
         * @return bool
         */
        public function validateCard(string $suit, string $name): bool
        {
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
            if ($card->suit === 'hearts' || $card->suit === 'diamonds') {
                return 'red';
            }

            return 'black';
        }

        /**
         * Gets the proper card value based on the name.
         *
         * @param Card $card
         * @return int
         */
        public function cardValue(Card $card): int
        {
            $index = array_search($card->getName(), $this->allowedNames, true);
            return ++$index;
        }

        /**
         * Return the deck size.
         *
         * @return int
         */
        public function deckSize(): int
        {
            return count($this->allowedNames) * count($this->allowedSuits);
        }

        /**
         * Picks all the playable cards based on the current playing stack
         * and the players hand, and, of course, the rules.
         *
         * @param DeckOfCards $playingStack
         * @param DeckOfCards $playerHand
         * @return DeckOfCards
         */
        public function pickPlayableCards(DeckOfCards $playingStack, DeckOfCards $playerHand): DeckOfCards
        {
            $playableCards = new DeckOfCards($this);

            $playingStackCards = $playingStack->getCards();
            $playerHandCards = $playerHand->getCards();

            $topCard = $playingStackCards[0];

            // Check all the player's cards and compare them to the top card of the playing stack.
            foreach ($playerHand as $playerCard) {
                if ($this->cardsMatch($playerCard, $topCard)) {
                    $playableCards->addCardOnTop($playerCard);
                }
            }

            return $playableCards;
        }

        /**
         * Checks if a player's card is a match and can be played.
         *
         * @param Card $cardToTest
         * @param Card $cardToCompare
         * @return bool
         */
        public function cardsMatch(Card $cardToTest, Card $cardToCompare): bool
        {
            foreach ($this->allowedMatches as $match) {
                $method = 'get'.ucfirst($match);
                if (method_exists($cardToTest, $method) && $cardToTest->{$method}() === $cardToCompare->{$method}()) {
                    return true;
                }
            }

            return false;
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
    }
