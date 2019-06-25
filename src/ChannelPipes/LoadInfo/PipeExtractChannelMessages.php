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

use Zend\Dom\Document;
use Zend\Dom\Document\Query;

class PipeExtractChannelMessages
{
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
            if (!$message) {
                $data->addMessage($message);
            }
        }

        return $next($data);
    }


    public function extractMessageData(\DOMElement $row): ?DataChannelMessage
    {
        foreach ($row->childNodes as $child) {
            if (!($child instanceof \DOMElement)) {
                continue;
            }
            $class = $child->attributes->getNamedItem('class');
            if (!$class) {
                continue;
            }
            $class = $class->nodeValue;
            if (strpos('tgme_widget_message_text', $class) !== false) {
                // extract message
            }

            if (strpos('tgme_widget_message_footer', $class) !== false) {
                // extract id
            }
        }
    }

}