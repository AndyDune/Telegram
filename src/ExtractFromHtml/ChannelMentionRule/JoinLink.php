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


namespace AndyDune\WebTelegram\ExtractFromHtml\ChannelMentionRule;


use AndyDune\WebTelegram\Check\IsJoinLink;

class JoinLink extends TmeLink
{
    use IsJoinLink;

    /**
     *
     * @param $text
     * @return array|bool
     */
    public function extract($text)
    {
        $result = [];
        $matches = [];
        $links = preg_match_all('|href="([^"]+)"|ui', $text, $matches);
        if (!$links or !array_key_exists(1, $matches)) {
            return false;
        }
        foreach ($matches[1] as $uri) {

            $path = $this->isJoinLink($uri);
            if ($path) {
                $result[] = $path;
            }
        }
        return $result;
    }

}