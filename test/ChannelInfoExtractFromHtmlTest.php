<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 14.09.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDuneTest\WebTelegram;

use AndyDune\WebTelegram\ExtractFromHtml\ChannelInfo;
use PHPUnit\Framework\TestCase;


/**
 * Public channels:
 * https://t.me/andydune_programming
 *
 * Groups:
 * https://t.me/ColoradoFurries
 * https://t.me/Beansoup
 * https://t.me/the_englishclub
 *
 * Persons:
 *
 *
 * Private (porno):
 * https://t.me/sunnyfans
 * https://t.me/bigtitsss
 * https://t.me/sweetmilfs
 * https://t.me/nudessafadex
 *
 * NotExists:
 * https://t.me/ColoradoFurries1
 *
 * Class ChannelInfoExtractFromHtmlTest
 * @package AndyDuneTest\WebTelegram
 */
class ChannelInfoExtractFromHtmlTest extends TestCase
{
    public function testSuccessExtract()
    {
        $info = new ChannelInfo(file_get_contents(__DIR__ . '/data/channel_info/normal.html'));
        $this->assertTrue($info->isSuccess());

    }

    public function testTypeExtract()
    {
        $info = new ChannelInfo(file_get_contents(__DIR__ . '/data/channel_info/normal.html'));
        $this->assertTrue($info->isSuccess());

        $this->assertEquals(ChannelInfo::TYPE_CHANNEL, $info->getType());

        $info = new ChannelInfo(file_get_contents(__DIR__ . '/data/channel_info/chat_1.html'));
        $this->assertTrue($info->isSuccess());

        $this->assertEquals(ChannelInfo::TYPE_GROUP, $info->getType());


        $info = new ChannelInfo(file_get_contents(__DIR__ . '/data/channel_info/no_data.html'));
        $this->assertTrue($info->isSuccess());

        $this->assertEquals(null, $info->getType());

        $info = new ChannelInfo(file_get_contents(__DIR__ . '/data/channel_info/person_1.html'));
        $this->assertTrue($info->isSuccess());
        $this->assertEquals(ChannelInfo::TYPE_PERSON, $info->getType());

        $info = new ChannelInfo(file_get_contents(__DIR__ . '/data/channel_info/person_wrong_1.html'));
        $this->assertTrue($info->isSuccess());
        $this->assertEquals(null, $info->getType());



        $info = new ChannelInfo(file_get_contents(__DIR__ . '/data/channel_info/private_1.html'));
        $this->assertTrue($info->isSuccess());

        $this->assertEquals(null, $info->getType());

    }

    public function testTypeExtractRequest()
    {
        $info = new ChannelInfo(file_get_contents('https://t.me/andydune_programming'));
        $this->assertTrue($info->isSuccess());

        $this->assertEquals(ChannelInfo::TYPE_CHANNEL, $info->getType());

        $info = new ChannelInfo(file_get_contents('https://t.me/the_englishclub'));
        $this->assertTrue($info->isSuccess());

        $this->assertEquals(ChannelInfo::TYPE_GROUP, $info->getType());

    }


}