<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 10.09.2019                            |
 * -----------------------------------------------
 *
 * test:
 * @see \AndyDuneTest\WebTelegram\ChannelPipesLoadInfoBriefPageTest
 */


namespace AndyDune\WebTelegram\ChannelPipes\LoadInfo;


class PipeLoadHtmlBriefPage extends PipeLoadHtml
{
    protected function getUrl($channelName)
    {
        $path = 'https://t.me/' . $channelName;
        return $path;
    }

}