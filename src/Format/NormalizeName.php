<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 30.07.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\Format;


trait NormalizeName
{
    public function normalizeName($name)
    {
        return mb_strtolower(trim($name), 'utf-8');
    }
}