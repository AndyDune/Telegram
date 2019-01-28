<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 28.01.2019                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\ExtractFromHtml\ChannelNameCheckRule;

use AndyDune\WebTelegram\Format\Channel\CheckChannelNameTrait;

class ChannelAllowSymbolsInName extends AbstractChannelNameCheck
{
    use CheckChannelNameTrait;

    public function check($channelName)
    {
        return $this->checkChannelName($channelName);
    }

}