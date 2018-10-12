<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 12.10.2018                            |
 * -----------------------------------------------
 *
 *
 * https://t.me/addstickers/WarcraftStickers
 */


namespace AndyDune\WebTelegram\Check;


trait IsStickerLink
{
    public function isStickerLink($link)
    {
        $parts = explode('addstickers/', $link);
        if (count($parts) != 2) {
            return false;
        }

        $parts[1] = rtrim($parts[1], '/');
        if (strlen($parts[1]) < 4) {
            return false;
        }

        return $parts[1];
    }
}