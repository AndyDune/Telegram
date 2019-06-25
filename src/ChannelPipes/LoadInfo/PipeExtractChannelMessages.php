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


        return $message;

        foreach ($row->childNodes as $child) {
            if (!($child instanceof \DOMElement)) {
                continue;
            }
            $class = $child->attributes->getNamedItem('class');
            if (!$class) {
                continue;
            }
            $class = $class->nodeValue;
            if (strpos($class, 'tgme_widget_message_text') !== false) {
                // extract message
            }

            if (strpos($class, 'tgme_widget_message_footer') !== false) {
                $this->doc = $this->domElementToDocument($child);
                $id = $this->extractId();

            }
        }
        return null;
    }


    protected function extractMessageText()
    {
        return $this->extractContentAsString('.tgme_widget_message_text', false);
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