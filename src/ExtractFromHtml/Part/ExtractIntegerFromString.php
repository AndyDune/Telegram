<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 25.08.2019                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\ExtractFromHtml\Part;


trait ExtractIntegerFromString
{
    protected function extractIntegerFromString($value)
    {
        $value = trim(preg_replace('|\s|i', '', $value));
        $matches = [];
        if (preg_match('|([.0-9]+)k$|i', $value, $matches)) {
            $value = $matches[1] * 1000;
        }
        if (preg_match('|([.0-9]+)m$|i', $value, $matches)) {
            $value = $matches[1] * 1000000;
        }

        return intval($value);
    }
}