<?php
    namespace MauMau\Generic;

    /**
    * Display class.
    */
    class CLIDisplay implements DisplayInterface
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
            if ($newLine) {
                $message .= PHP_EOL;
            }

            fwrite(STDOUT, $message);
        }
    }
