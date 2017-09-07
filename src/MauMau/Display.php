<?php
    namespace MauMau;

    /**
    * Display class.
    */
    class Display
    {
        /**
         * Prints a message.
         *
         * @param string $message
         * @param bool $newLine (optional) Finish with a new line.
         * @return void
         */
        public function message(string $message, bool $newLine = true)
        {
            echo $message;
            if ($newLine) {
                echo "\n";
            }
        }
    }
