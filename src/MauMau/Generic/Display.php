<?php
    namespace MauMau\Generic;

    /**
    * Display class.
    */
    class Display implements DisplayInterface
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
            $cli = php_sapi_name() === "cli"; // Check for command line mode
            if ($newLine) {
                $message .=  $cli ? PHP_EOL : '<br>';
            }

            if ($cli) {
                fwrite(STDOUT, $message);
            } else {
                echo $message;
            }
        }
    }
