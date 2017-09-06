<?php
    namespace MauMau;

    /**
    * Rules class.
    */
    class Rules
    {
        const ALLOWED_SUITS = ['hearts', 'diamonds', 'spades', 'clubs'];
        const ALLOWED_NAMES = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
        const MIN_PLAYERS = 2;
        const MAX_PLAYERS = 4;
        const HAND_SIZE = 7;
        const PLAY = 'CLOCKWISE';

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
            if (!in_array($suit, self::ALLOWED_SUITS)) {
                return false;
            }

            if (!in_array($name, self::ALLOWED_NAMES)) {
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
            return $players >= self::MIN_PLAYERS &&
                $players <= self::MAX_PLAYERS &&
                ($players * self::HAND_SIZE < $this->deckSize());
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
            $index = array_search($card->getName(), self::ALLOWED_NAMES, true);
            return ++$index;
        }

        /**
         * Return the deck size.
         *
         * @return int
         */
        public function deckSize(): int
        {
            return count(self::ALLOWED_NAMES) * count(self::ALLOWED_SUITS);
        }

        /**
         * Picks all the playable cards based on the current playing stack
         * and the players hand, and, of course, the rules.
         *
         * @param DeckOfCards $playingStack
         * @param DeckOfCards $playersHand
         * @return DeckOfCards
         */
        public function pickPlayableCards(DeckOfCards $playingStack, DeckOfCards $playersHand): DeckOfCards
        {
            $playableCards = new DeckOfCards($this);

            $playingStackCards = $playingStack->getCards();
            $playersHandCards = $playersHand->getCards();

            $topCard = $playingStackCards[0];

            // Check all the player's cards and compare them to the top card of the playing stack.
            foreach ($playersHandCards as $playersCard) {
                if ($this->cardIsPlayable($playersCard, $topCard)) {
                    $playableCards->addCardOnTop($playersCard);
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
        protected function cardIsPlayable(Card $cardToTest, Card $cardToCompare): bool
        {
            foreach ($this->allowedMatches as $match) {
                $method = 'get'.ucfirst($match);
                if (method_exists($cardToTest, $method) && $cardToTest->{$method}() === $cardToCompare->{$method}()) {
                    return true;
                }
            }

            return false;
        }
    }
