<?php
    namespace MauMau\Generic;

    /**
    * Display Interface.
    */
    interface DisplayInterface
    {
        public function message(string $message, bool $newLine = true);
    }
