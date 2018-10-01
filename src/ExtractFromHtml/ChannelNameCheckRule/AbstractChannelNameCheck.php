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


abstract class AbstractChannelNameCheck
{

    abstract public function check($channelName);

    public function __invoke($channelName)
    {
        return $this->check($channelName);
    }

}