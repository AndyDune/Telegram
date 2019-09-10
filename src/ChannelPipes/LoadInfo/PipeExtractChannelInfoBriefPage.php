<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 10.09.2019                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\ChannelPipes\LoadInfo;


use AndyDune\WebTelegram\ExtractFromHtml\Part\ExtractIntegerFromString;
use AndyDune\WebTelegram\ExtractFromHtml\Part\ExtractWithDomTrait;
use Zend\Dom\Document;
use Zend\Dom\Document\Query;

class PipeExtractChannelInfoBriefPage
{
    use ExtractWithDomTrait;
    use ExtractIntegerFromString;

    protected $channelInfoTitle = [
        '.tgme_page_post .tgme_page_title',
    ];

    protected $personInfoTitle = [
        '.tgme_page .tgme_page_title',
    ];

    protected $channelInfoDescription = '.tgme_page_post .tgme_page_description';

    protected $channelInfoImage = '.tgme_page_post .tgme_page_photo img';

    protected $channelInfoExtra = '.tgme_page_post .tgme_page_extra';
    protected $personInfoExtra = '.tgme_page .tgme_page_extra';

    public function __invoke(Data $data, callable $next)
    {
        $this->doc = new Document($data->getHtmlBody());
        $this->doc->setEncoding('UTF-8');

        // заголовок есть всегда
        $title = $this->extractContentAsString($this->channelInfoTitle);
        if ($title === false) {
            $title = $this->extractContentAsString($this->personInfoTitle);
            if ($title) {
                $data->setPersonTitle($title);
                $value = $this->extractContentAsString($this->personInfoExtra);
                if ($value) {
                    $data->setPersonExtra($value);
                }
                $data->setErrorMessage('It is probably person.');
                $data->setErrorCode(Data::ERROR_CONTENT_PROBABLY_PERSON);
                $data->setErrorPlace(PipeExtractChannelInfoInfoPage::class);
                return $data;
            }
        }

        if ($title === false) {
            $data->setErrorMessage('No channel title was found.');
            $data->setErrorCode(Data::ERROR_CONTENT_NO_CHANNEL_TITLE);
            $data->setErrorPlace(PipeExtractChannelInfoInfoPage::class);
            return $data;
        }
        $data->setChannelTitle($title);

        $value = $this->extractContentAsHtml($this->channelInfoDescription);
        if ($value) {
            $data->setChannelDescription($value);
        }

        $value = $this->extractAttribute($this->channelInfoImage, 'src');
        if ($value) {
            $data->setChannelImageUrl($value);
        }

        $value = $this->extractContentAsString($this->channelInfoExtra);
        if ($value) {
            $this->extractCounts($value, $data);
        }

        return $next($data);
    }

    protected function extractCounts($string, Data $data)
    {
        $parts = explode(',', $string);
        $data->setChannelCountData(count($parts));
        foreach($parts as $part) {
            if (preg_match('|members|ui', $part)) {
                $part = preg_replace('|[a-z]{2,}|ui', '', $part);
                $part = preg_replace('|\s|', '', $part);
                if ($part) {
                    $part = $this->extractIntegerFromString($part);
                }
                if ($part) {
                    $data->setChannelMemberCount($part);
                }
                continue;
            }

            if (preg_match('|online|ui', $part)) {
                $part = preg_replace('|[a-z]{2,}|ui', '', $part);
                $part = preg_replace('|\s|', '', $part);
                if ($part) {
                    $part = $this->extractIntegerFromString($part);
                }
                if ($part) {
                    $data->setChannelMemberOnlineCount($part);
                }
                continue;
            }

        }
    }
}