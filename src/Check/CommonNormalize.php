<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 12.10.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\Check;
use voku\helper\UTF8;

trait CommonNormalize
{
    public function commonNormalize($name)
    {
        return trim(strtolower($name));
    }

    public function commonNormalizeUtf($name)
    {
        return UTF8::strtolower(trim($name));
    }
}