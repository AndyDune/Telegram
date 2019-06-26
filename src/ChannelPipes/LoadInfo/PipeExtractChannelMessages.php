<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 25.06.2019                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\ChannelPipes\LoadInfo;

use AndyDune\WebTelegram\ExtractFromHtml\Part\DomElementToDocument;
use AndyDune\WebTelegram\ExtractFromHtml\Part\ExtractWithDomTrait;
use Zend\Dom\Document;
use Zend\Dom\Document\Query;

class PipeExtractChannelMessages
{
    use DomElementToDocument, ExtractWithDomTrait;
    /**
     * @var Document
     */
    protected $doc;

    protected $messagesPath = '.tgme_main .tgme_widget_message_bubble';


    public function __invoke(Data $data, callable $next)
    {
        $this->doc = new Document($data->getHtmlBody());
        $this->doc->setEncoding('UTF-8');

        $res = Query::execute($this->messagesPath, $this->doc, Document\Query::TYPE_CSS);
        if (!count($res)) {
            return $next($data);
        }

        foreach ($res as $row) {
            $message = $this->extractMessageData($row);
            if ($message) {
                $data->addMessage($message);
            }
        }

        return $next($data);
    }


    public function extractMessageData(\DOMElement $row): ?DataChannelMessage
    {
        $this->doc = $this->domElementToDocument($row);

        $id = $this->extractId();
        if (!$id) {
            return null;
        }

        $message = new DataChannelMessage($id);

        $text = $this->extractMessageText();
        $message->setText($text);

        $date = $this->extractDate();
        if ($date) {
            $message->setDateTime($date);
        }

        $value = $this->extractDocumentTitle();
        if ($value) {
            $message->setDocumentTitle($value);
        }

        $value = $this->extractDocumentExtra();
        if ($value) {
            $message->setDocumentExtra($value);
        }

        $value = $this->extractDocumentViewsCount();
        if ($value) {
            $message->setViewsCount((int)$value);
        }

        $value = $this->extractStickerImagePath();
        if ($value) {
            $message->setStickerImage($value);
        }

        $value = $this->extractMessageVoice();
        if ($value) {
            $message->setMessageVoice($value);
        }


        return $message;
    }


    protected function extractDocumentViewsCount()
    {
        return $this->extractContentAsString('.tgme_widget_message_info .tgme_widget_message_views');
    }

    protected function extractStickerImagePath()
    {
        $style = $this->extractAttribute('.tgme_widget_message_sticker_wrap .tgme_widget_message_sticker', 'style');
        if (!$style) {
            return null;
        }

        $matches = [];
        if (!preg_match("|url\('([^']{5,})'\)|ui", $style, $matches)) {
            return null;
        }
        return $matches[1];
    }

    protected function extractMessageVoice()
    {
        return $this->extractAttribute('.tgme_widget_message_voice', 'src');
    }


    protected function extractDocumentTitle()
    {
        return $this->extractContentAsString('.tgme_widget_message_document_title');
    }

    protected function extractDocumentExtra()
    {
        return $this->extractContentAsString('.tgme_widget_message_document_extra');
    }


    protected function extractMessageText()
    {
        return $this->extractContentAsHtml('.tgme_widget_message_text');
    }


    /**
     * Пример:
     * 2019-01-22T12:10:23+00:00
     *
     * @return bool|\DateTime|string
     */
    protected function extractDate()
    {
        $date = $this->extractAttribute('.tgme_widget_message_info time', 'datetime');
        if ($date) {
            $date = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $date);
        }
        return $date;
    }


    protected function extractId()
    {
        $link = $this->extractAttribute('.tgme_widget_message_info .tgme_widget_message_date', 'href');
        if (!$link) {
            return null;
        }
        $parts = explode('/', trim($link, ' /'));
        if (count($parts) < 2) {
            return null;
        }
        return (int)array_pop($parts);
    }

}