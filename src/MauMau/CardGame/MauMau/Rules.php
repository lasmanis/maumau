<?php
    namespace MauMau\CardGame\MauMau;

    use MauMau\CardGame\AbstractRules;
    use MauMau\CardGame\Card;
    use MauMau\CardGame\DeckOfCards;

    /**
    * Rules class.
    */
    class Rules extends AbstractRules
    {
        protected $jokers = 0;
        protected $allowedMatches = ['suit', 'value'];

        /**
         * Gets the proper card value based on the name.
         *
         * @param Card $card
         * @return int
         */
        public function cardValue(Card $card): int
        {
            if ($card->isJoker()) {
                return 0;
            }
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
            return count($this->allowedNames) * count($this->allowedSuits) + ($this->jokers ? 2 : 0);
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
            if ($cardToTest->isJoker() || $cardToCompare->isJoker()) {
                return true;
            }

            foreach ($this->allowedMatches as $match) {
                $method = 'get'.ucfirst($match);
                if (method_exists($cardToTest, $method) && $cardToTest->{$method}() === $cardToCompare->{$method}()) {
                    return true;
                }
            }

            return false;
        }
    }
