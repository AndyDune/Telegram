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
 * Groups:
 * https://t.me/ColoradoFurries
 * https://t.me/Beansoup
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
}