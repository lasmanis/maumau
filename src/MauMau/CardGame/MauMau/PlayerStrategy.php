<?php
    namespace MauMau\CardGame\MauMau;

    use MauMau\CardGame\Card;
    use MauMau\CardGame\DeckOfCards;
    use MauMau\CardGame\AbstractRules;

    /**
    * PlayerStrategy class
    */
    class PlayerStrategy
    {
        private $rules;

        public function __construct(AbstractRules $rules)
        {
            $this->rules = $rules;
        }

        /**
         * Selects the best card to play.
         *
         * @param DeckOfCards $playableCards
         * @param DeckOfCards $playerHand
         * @return Card
         */
        public function pickCard(DeckOfCards $playableCards, DeckOfCards $playerHand): Card
        {
            // how many matches does each card have?
            // Choose the one with the most matches
            $cardWithMaxMatches = null;
            $maxMatches = 0;

            foreach ($playableCards as $playableCard) {
                $matches = 0;
                foreach ($playerHand as $playerCard) {
                    if ($this->rules->cardsMatch($playableCard, $playerCard)) {
                        $matches++;
                    }
                }

                if ($matches >= $maxMatches) {
                    $maxMatches = $matches;
                    $cardWithMaxMatches = $playableCard;
                }
            }

            $playerHand->removeCard($cardWithMaxMatches);

            return $cardWithMaxMatches;
        }
    }
