<?php

namespace MauMau\Generic;

class BrowserDisplayTest extends DisplayTestBase
{
    public function setUp()
    {
        $this->display = new BrowserDisplay();
    }

    /**
     * @covers \MauMau\Generic\BrowserDisplay::message
     */
    public function testSimpleMessage()
    {
        parent::testSimpleMessage();
    }
}
