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


namespace AndyDune\WebTelegram\ExtractFromHtml\ChannelNameCheckRule;


class IsNotBot extends AbstractChannelNameCheck
{
    public function check($channelName)
    {
        if (preg_match('|bot$|ui', $channelName)) {
            return false;
        }
        return true;
    }
}