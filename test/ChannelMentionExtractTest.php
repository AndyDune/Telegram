<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 04.10.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDuneTest\WebTelegram;
use AndyDune\WebTelegram\Check\IsStickerLink;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelMentionRule\TmeLink;
use PHPUnit\Framework\TestCase;


class ChannelMentionExtractTest extends TestCase
{
    public function testRules()
    {
        $tmeRule = new TmeLink();
        $names = $tmeRule->extract(file_get_contents(__DIR__ . '/data/messages_content/have_link_1.phtml'));
        $this->assertCount(1, $names);
        $this->assertTrue(in_array('tv360ru', $names));
    }

    public function testIsStickerLink()
    {
        $instance = new class() {
            use IsStickerLink;
        };
        $link = 'https://t.me/addstickers/WarcraftStickers';
        $this->assertEquals('WarcraftStickers', $instance->isStickerLink($link));

        $link = 'addstickers/WarcraftStickers';
        $this->assertEquals('WarcraftStickers', $instance->isStickerLink($link));

        $link = 'https://t.me/addstickers_/WarcraftStickers';
        $this->assertFalse($instance->isStickerLink($link));

        $link = 'https://t.me/addstickers_/WarcraftStickers/1';
        $this->assertFalse($instance->isStickerLink($link));

        $link = 'WarcraftStickers/1';
        $this->assertFalse($instance->isStickerLink($link));
    }
}