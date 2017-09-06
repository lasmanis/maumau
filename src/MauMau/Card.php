<?php
    namespace MauMau;

/**
    * Card class
    */
    class Card
    {
        private $rules;
        private $suit;
        private $name;

        /**
         * Creates a new card.
         *
         * @param string $suit
         * @param string $name
         * @throws InvalidCardException
         * @return Card
         */
        public function __construct(string $suit, string $name, Rules $rules)
        {
            $this->rules = $rules;

            if (!$this->rules->validateCard($suit, $name)){
                throw new \Exception('Invalid card');
            }

            $this->suit = $suit;
            $this->name = $name;
        }

        /**
         * Get the card's color.
         *
         * @return string
         */
        public function getColor(): string
        {
            return $this->rules->cardColor($this);
        }

        /**
         * Get the card's value.
         *
         * @return int
         */
        public function getValue(): int
        {
            return $this->rules->cardValue($this);
        }

        /**
         * Get the card's suit.
         *
         * @return string
         */
        public function getSuit(): string
        {
            return $this->suit;
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
         * Displays the Card in a human friendly way.
         *
         * @return string
         */
        public function __toString(): string
        {
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
                    $displayName = "invalid card";
                    return $displayName;
            }

            $displayName .= $this->name;

            return $displayName;
        }
    }
