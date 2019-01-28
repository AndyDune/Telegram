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
use AndyDune\WebTelegram\ExtractFromHtml\ChannelMention;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelNameCheckRule\ChannelAllowSymbolsInName;
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

    /**
     * @covers ChannelAllowSymbolsInName::check
     */
    public function testChannelNameAllow()
    {
        $check = new ChannelAllowSymbolsInName();
        $this->assertTrue($check->check('onetwobotnot'));
        $this->assertTrue($check('onetwobot_not'));
        $this->assertTrue($check('andydune_english'));
        $this->assertFalse($check('onetwobot.not'));
        $this->assertFalse($check('привет'));
        $this->assertFalse($check('beauty\\_shopper.%0AКатя%20почти%20каждый%20день%20пишет%20где%20и%20на%20какие%20марки%20косметоса%20есть%20сейлы%20(от%20дорогих,%20до%20масс-маркета'));
    }


    public function testInComplex()
    {
        $extractor = new ChannelMention();
        $text = 'https://t.me/addstickers/WarcraftStickers';
        $this->assertEquals(1, $extractor->handle($text));
        $this->assertEquals('WarcraftStickers', $extractor->getFindStickers()['warcraftstickers']);

        $extractor = new ChannelMention();
        $text = '
        https://t.me/addstickers/WarcraftStickers
        https://t.me/addstickers/Warcraft_Stickers
        ';
        $this->assertEquals(2, $extractor->handle($text));
        $this->assertEquals('WarcraftStickers', $extractor->getFindStickers()['warcraftstickers']);
        $this->assertEquals('Warcraft_Stickers', $extractor->getFindStickers()['warcraft_stickers']);

        $extractor = new ChannelMention();
        $text = '
        https://t.me/addstickers/WarcraftStickers
        
        <a href="https://t.me/karaulny" target="_blank">@karaulny</a> Караульный 83000 <br/>
        <a href="https://t.me/karaulny" target="_blank">@karaulny</a> Караульный 83000 <br/>
        adsadsadsad
        <a href="https://t.me/beauty\\_shopper.%0AКатя%20почти%20каждый%20день%20пишет%20где%20и%20на%20какие%20марки%20косметоса%20есть%20сейлы%20(от%20дорогих,%20до%20масс-маркета" target="_blank">@some</a> Wrong <br/>
        adasdasdsad
        addasd
        <a href="https://t.me/karaulnybot" target="_blank">@bot</a> Бот <br/>
        
        <a href="https://t.me/joinchat/SasaS_adjkasndsahh" target="_blank">@karaulny</a> Караульный 83000 <br/>
        
        https://t.me/addstickers/Warcraft_Stickers
        ';
        $this->assertEquals(4, $extractor->handle($text));
        $this->assertEquals('WarcraftStickers', $extractor->getFindStickers()['warcraftstickers']);
        $this->assertEquals('Warcraft_Stickers', $extractor->getFindStickers()['warcraft_stickers']);
        $this->assertEquals('karaulny', $extractor->getFindChannels()['karaulny']);
        $this->assertEquals('joinchat/SasaS_adjkasndsahh', $extractor->getFindJoinLink()['joinchat/sasas_adjkasndsahh']);
    }

}