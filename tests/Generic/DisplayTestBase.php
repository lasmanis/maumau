<?php

namespace MauMau\Generic;

use PHPUnit\Framework\TestCase;

class DisplayTestBase extends TestCase
{
    protected $display;

    public function testSimpleMessage()
    {
        $this->expectOutputRegex('/^message/i');
        $this->display->message('Message');
    }
}
