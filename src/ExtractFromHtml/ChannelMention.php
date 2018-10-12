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


use AndyDune\WebTelegram\Check\CommonNormalize;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelMentionRule\AbstractRule;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelMentionRule\JoinLink;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelMentionRule\TmeLink;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelNameCheckRule\IsNotBot;

class ChannelMention
{
    use CommonNormalize;

    protected $rules = [
        TmeLink::class,
        JoinLink::class
    ];

    protected $checks = [
        IsNotBot::class
    ];

    protected $findChannels = [];
    protected $findJoinLink = [];

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
        $count = 0;
        /** @var AbstractRule[] $rules */
        $rules = $this->getRules();
        foreach($rules as $rule) {
            $links = $rule->extract($text);
            if (!$links) {
                continue;
            }

            switch ($rule->getType()) {
                case 'channels':
                    $count += $this->addChannels($links);
                    break;
                case 'join_links':
                    $count += $this->addJoinLinks($links);
                    break;

            }
        }
        return $count;
    }

    protected function addChannels($links)
    {
        $count = 0;
        foreach ($links as $link) {
            $normal = $this->commonNormalize($link);
            if (!array_key_exists($normal, $this->findChannels)) {
                $count++;
                $this->findChannels[$normal] = $link;
            }
        }
        return $count;
    }

    protected function addJoinLinks($links)
    {
        $count = 0;
        foreach ($links as $link) {
            $normal = $this->commonNormalize($link);
            if (!array_key_exists($normal, $this->findJoinLink)) {
                $count++;
                $this->findJoinLink[$normal] = $link;
            }
        }
        return $count;

    }

}