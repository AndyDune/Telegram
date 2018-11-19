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