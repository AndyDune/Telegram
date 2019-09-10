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


namespace AndyDune\WebTelegram\ExtractFromHtml\Part;


use DOMAttr, DOMElement;
use Zend\Dom\Document;
use Zend\Dom\Document\Query;

trait ExtractWithDomTrait
{
    /**
     * @var Document
     */
    protected $doc;

    protected function extractContentAsString($path, $noHtml = true)
    {
        if (!is_array($path)) {
            $path = [$path];
        }

        foreach ($path as $pathRow) {
            $res = Query::execute($pathRow, $this->doc, Document\Query::TYPE_CSS);
            if ($res->count()) {
                /** @var \DOMNodeList $content */
                $content = current($res);
                $string = $content->item(0)->nodeValue;
                if ($noHtml) {
                    $string = strip_tags($string);
                }
                return trim($string);
            }
        }
        return false;
    }

    protected function extractContentAsHtml($path)
    {
        if (!is_array($path)) {
            $path = [$path];
        }

        foreach ($path as $pathRow) {
            $res = Query::execute($pathRow, $this->doc, Document\Query::TYPE_CSS);
            if ($res->count()) {
                $string = '';
                /** @var \DOMNodeList $content */
                $content = current($res);
                $item = $content->item(0);
                if (!($item instanceof DOMElement)) {
                    continue;
                }
                foreach ($item->childNodes as $child) {
                    $string .= $item->ownerDocument->saveXML( $child );
                }

                //$string = $this->doc->getDomDocument()->saveXML($content->item(0));
                return $string;
            }
        }
        return false;

    }


    protected function extractAttribute($path, $attribute)
    {
        if (!is_array($path)) {
            $path = [$path];
        }

        foreach ($path as $pathRow) {
            $res = Query::execute($pathRow, $this->doc, Document\Query::TYPE_CSS);
            if (!$res->count()) {
                continue;
            }
            /** @var \DOMNodeList $content */
            $content = current($res);
            /** @var DOMAttr $attr */
            $attr = $content->item(0)->attributes->getNamedItem($attribute);
            if ($attr instanceof DOMAttr) {
                return $attr->value;
            }
        }
        return false;
    }

}