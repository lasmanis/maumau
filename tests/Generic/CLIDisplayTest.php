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
        $res = $this->display->message('');
        $this->assertEquals(2, $res);
    }
}
