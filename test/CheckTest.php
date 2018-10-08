<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 08.10.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDuneTest\WebTelegram;
use AndyDune\WebTelegram\Check\IsJoinLink;
use PHPUnit\Framework\TestCase;


class CheckTest extends TestCase
{
    /**
     * @covers IsJoinLink::isJoinLink
     */
    public function testIsJoinLink()
    {
        $instance = new class {
            use IsJoinLink;
        };
        $link = 'https://t.me/joinchat/AAAAAFGLv49cumwU5OPZ_Q';
        // https://t.me/joinchat/AAAAAFGLv49cumwU5OPZ_Q
        $this->assertEquals('joinchat/AAAAAFGLv49cumwU5OPZ_Q', $instance->isJoinLink($link));

        $link = 't.me/joinchat/AAAAAFGLv49cumwU5OPZ_Q';
        // https://t.me/joinchat/AAAAAFGLv49cumwU5OPZ_Q
        $this->assertEquals('joinchat/AAAAAFGLv49cumwU5OPZ_Q', $instance->isJoinLink($link));

        $link = '/joinchat/AAAAAFGLv49cumwU5OPZ_Q';
        // https://t.me/joinchat/AAAAAFGLv49cumwU5OPZ_Q
        $this->assertEquals('joinchat/AAAAAFGLv49cumwU5OPZ_Q', $instance->isJoinLink($link));

        $link = 'joinct/AAAAAFGLv49cumwU5OPZ_Q';
        // https://t.me/joinchat/AAAAAFGLv49cumwU5OPZ_Q
        $this->assertEquals(false, $instance->isJoinLink($link));

        $link = 'joinchat/Z_Q';
        // https://t.me/joinchat/AAAAAFGLv49cumwU5OPZ_Q
        $this->assertEquals(false, $instance->isJoinLink($link));

    }
}