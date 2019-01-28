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


namespace AndyDune\WebTelegram\Format\Channel;


trait CheckChannelNameTrait
{
    public function checkChannelName($name)
    {
        $hasBadSymbols = preg_match('|[^_a-z0-9]|ui', $name);
        if ($hasBadSymbols) {
            return false;
        }
        return true;
    }
}