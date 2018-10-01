<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 01.10.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDuneTest\WebTelegram;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelNameCheckRule\IsNotBot;
use PHPUnit\Framework\TestCase;


class ChannelMentionTest extends TestCase
{
    public function testChannelNameCheckIsNotBot()
    {
        $check = new IsNotBot();
        $this->assertTrue($check->check('onetwobotnot'));
        $this->assertFalse($check->check('onetwobot'));
        $this->assertTrue($check('onetwobot_not'));
        $this->assertFalse($check('onetwo_bot'));
    }
}