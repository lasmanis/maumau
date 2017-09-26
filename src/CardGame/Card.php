<?php
    namespace MauMau\CardGame;

    use MauMau\CardGame\AbstractRules;

    /**
    * Card class
    */
    class Card
    {
        protected $rules;
        protected $suit;
        protected $name;
        protected $color;
        protected $value;

        /**
         * The class constructor.
         *
         * @param string $suit
         * @param string $name
         * @param AbstractRules $rules
         * @throws \Exception if card is not validated
         */
        public function __construct(string $suit, string $name, AbstractRules $rules)
        {
            $this->rules = $rules;

            if (!$this->rules->validateCard($suit, $name)) {
                throw new \Exception('Invalid card');
            }

            $this->suit = $suit;
            $this->name = $name;
            $this->value = $this->rules->cardValue($this);
            $this->color = $this->rules->cardColor($this);
        }

        /**
         * Get the card's color.
         *
         * @return string
         */
        public function getColor(): string
        {
            return $this->color;
        }

        /**
         * Get the card's value.
         *
         * @return int
         */
        public function getValue(): int
        {
            return $this->value;
        }

        /**
         * Get the card's suit.
         *
         * @return string
         */
        public function getSuit(): string
        {
            return $this->isJoker() ? 'any' : $this->suit;
        }

        /**
         * Get the card's name.
         *
         * @return string
         */
        public function getName(): string
        {
            return $this->name;
        }

        /**
         * Checks if the card is a Joker
         *
         * @return bool
         */
        public function isJoker(): bool
        {
            return $this->name === 'Joker';
        }

        /**
         * Displays the Card in a human friendly way.
         *
         * @return string
         */
        public function __toString(): string
        {
            if ($this->isJoker()) {
                return $this->name;
            }

            $displayName = "";
            switch ($this->suit) {
                case 'hearts':
                    $displayName = "\u{2665}";
                    break;
                case 'clubs':
                    $displayName = "\u{2663}";
                    break;
                case 'spades':
                    $displayName = "\u{2660}";
                    break;
                case 'diamonds':
                    $displayName = "\u{2666}";
                    break;

                default:
                    return "invalid card";
            }

            return $displayName .= $this->name;
        }
    }
