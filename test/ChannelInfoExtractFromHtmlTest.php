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


class ChannelInfoExtractFromHtmlTest extends TestCase
{
    public function testSuccessExtract()
    {
        $info = new ChannelInfo(file_get_contents(__DIR__ . '/data/channel_info/normal.html'));
        $this->assertTrue($info->isSuccess());
    }
}