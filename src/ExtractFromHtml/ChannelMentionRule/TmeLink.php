<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 04.10.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\ExtractFromHtml\ChannelMentionRule;


class TmeLink extends AbstractRule
{
    protected $domains = ['t.me', 'telegram.me'];

    /**
     * Проверить человека <a href="https://t.me/yurisenik" target="_blank">@yurisenik</a>
     * через бота
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
            $parts = parse_url($uri);
            if (!array_key_exists('host', $parts)) {
                continue;
            }
            if (!in_array($parts['host'], $this->domains)) {
                continue;
            }
            if (!array_key_exists('path', $parts)) {
                continue;
            }

            $path = trim($parts['path'], '/ ');
            if (strpos('/', $path)) {
                continue;
            }
            $result[] = $path;
        }

        return $result;
    }
}