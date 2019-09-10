<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 25.06.2019                            |
 * -----------------------------------------------
 *
 * test:
 * @see \AndyDuneTest\WebTelegram\ChannelPipesLoadInfoTest
 */


namespace AndyDune\WebTelegram\ChannelPipes\LoadInfo;

use AndyDune\WebTelegram\ExtractFromHtml\Part\ExtractIntegerFromString;
use AndyDune\WebTelegram\ExtractFromHtml\Part\ExtractWithDomTrait;
use Zend\Dom\Document\Query;
use Zend\Dom\Document;

class PipeExtractChannelInfo
{
    use ExtractWithDomTrait;
    use ExtractIntegerFromString;


    protected $channelInfoTitle = [
        '.tgme_channel_info .tgme_channel_info_header_title .auto',
        '.tgme_channel_info .tgme_channel_info_header_title'
    ];
    protected $channelInfoDescription = '.tgme_channel_info .tgme_channel_info_description';

    protected $channelInfoImage = '.tgme_channel_info .tgme_page_photo_image img';

    protected $channelInfoCounts = '.tgme_channel_info .tgme_channel_info_counters .tgme_channel_info_counter';


    public function __invoke(Data $data, callable $next)
    {
        $this->doc = new Document($data->getHtmlBody());
        $this->doc->setEncoding('UTF-8');

        // заголовок есть всегда
        $title = $this->extractContentAsString($this->channelInfoTitle);
        if ($title === false) {
            $data->setErrorMessage('No channel title was found.');
            $data->setErrorCode(Data::ERROR_CONTENT_NO_CHANNEL_TITLE);
            $data->setErrorPlace(PipeExtractChannelInfo::class);
            return $data;
        }
        $data->setChannelTitle($title);

        $value = $this->extractContentAsString($this->channelInfoDescription);
        if ($value) {
            $data->setChannelDescription($value);
        }

        $value = $this->extractAttribute($this->channelInfoImage, 'src');
        if ($value) {
            $data->setChannelImageUrl($value);
        }

        $res = Query::execute($this->channelInfoCounts, $this->doc, Document\Query::TYPE_CSS);
        $count = $res->count();
        $data->setChannelCountData($count);

        if ($count) {
            $this->findCount($res, $data);
        }

        return $next($data);
    }

    protected function findCount(Document\NodeList $res, Data $data)
    {
        foreach($res as $row) {
            $type = '';
            $value = 0;
            /** @var \DOMNodeList $children */
            $children = $row->childNodes;
            /** @var \DOMElement $part */
            foreach ($children as $part) {
                if (!($part instanceof \DOMElement)) {
                    continue;
                }

                $class = $part->attributes->getNamedItem('class');
                if (!$class) {
                    continue;
                }
                $class = $class->nodeValue;
                if (strpos('counter_value', $class) !== false) {
                    $value = $this->extractIntegerFromString($part->nodeValue);
                    continue;
                }

                if (strpos('counter_type', $class) !== false) {
                    $type = $part->nodeValue;
                    continue;
                }
            }

            if (!$type) {
                continue;
            }

            switch ($type) {
                case 'members':
                    $data->setChannelMemberCount($value);
                    break;
                case 'photos':
                    $data->setChannelPhotoCount($value);
                    break;
                case 'links':
                    $data->setChannelLinkCount($value);
                    break;
                case 'file':
                    $data->setChannelFileCount($value);
                    break;
            }
        }
    }


}