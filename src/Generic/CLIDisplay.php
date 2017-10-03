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
         * @return mixed Number of bytes written by fwrite, or false in case of failure.
         */
        public function message(string $message, bool $newLine = true)
        {
            if ($newLine) {
                $message .= PHP_EOL;
            }

            return fwrite(STDOUT, $message);
        }
    }
