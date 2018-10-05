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


namespace AndyDune\WebTelegram\ExtractFromHtml;


use AndyDune\WebTelegram\ExtractFromHtml\ChannelMentionRule\TmeLink;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelNameCheckRule\IsNotBot;

class ChannelMention
{
    protected $rules = [
        TmeLink::class
    ];

    protected $checks = [
        IsNotBot::class
    ];

    protected function getRules()
    {
        foreach($this->rules as $key =>  $rule) {
            if (is_string($rule)) {
                $rule = new $rule;
                $this->rules[$key] = $rule;
            }
        }
        return $this->rules;
    }

    public function handle($text)
    {

    }

}