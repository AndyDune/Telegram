<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 08.10.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\Check;


trait IsJoinLink
{
    public function isJoinLink($name)
    {
        $parts = explode('joinchat/', $name);
        if (count($parts) != 2) {
            return false;
        }

        $parts[1] = rtrim($parts[1], '/');
        if (strlen($parts[1]) < 4) {
            return false;
        }

        $name = 'joinchat/' . $parts[1];

        $matches = [];
        if (!preg_match('#(^joinchat/|/joinchat/)([-_a-z0-9]+)$#ui', $name, $matches)) {
            return false;
        }

        return 'joinchat/' . $matches[2];
    }

}