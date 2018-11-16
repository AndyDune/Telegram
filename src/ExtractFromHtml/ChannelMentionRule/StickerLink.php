<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 16.11.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\ExtractFromHtml\ChannelMentionRule;


use AndyDune\WebTelegram\Check\IsStickerLink;

class StickerLink extends AbstractRule
{
    use IsStickerLink;
    protected $type = 'stickers';

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
        $links = preg_match_all('|(t.me/[-_a-z0-9/]+)|ui', $text, $matches);
        if (!$links or !array_key_exists(1, $matches)) {
            return false;
        }
        foreach ($matches[1] as $uri) {
            $path = $this->isStickerLink($uri);
            if ($path) {
                $result[] = $path;
            }

        }
        return $result;
    }
}