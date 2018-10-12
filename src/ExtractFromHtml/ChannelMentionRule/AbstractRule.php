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


namespace AndyDune\WebTelegram\ExtractFromHtml\ChannelMentionRule;


abstract class AbstractRule
{
    protected $type = '';

    abstract public function extract($text);

    public function __invoke($text)
    {
        return $this->extract($text);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

}