<?php

namespace MauMau\Generic;

class CLIDisplayTest extends DisplayTestBase
{
    public function setUp()
    {
        $this->display = new CLIDisplay();
    }

    /**
     * @covers \MauMau\Generic\CLIDisplay::message
     */
    public function testSimpleMessage()
    {
        $os = php_uname();
        $newLineBytes = substr($os, 0, strlen('Windows')) === 'Windows' ? 2 : 1;
        $res = $this->display->message('');
        $this->assertEquals($newLineBytes, $res);
    }
}
