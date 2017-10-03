<?php
    namespace MauMau\Generic;

    /**
    * Display class.
    */
    class BrowserDisplay implements DisplayInterface
    {
        /**
         * Prints a message.
         *
         * @param string $message
         * @param bool $newLine (optional) Finish with a new line.
         * @return mixed
         */
        public function message(string $message, bool $newLine = true)
        {
            if ($newLine) {
                $message .= '<br>';
            }

            echo $message;

            return true;
        }
    }
