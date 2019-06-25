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

use Zend\Dom\Document;
use DOMElement, DOMDocument;

trait DomElementToDocument
{
    /**
     * @param DOMElement $element
     * @return Document
     */
    protected function domElementToDocument(DOMElement $element): Document
    {
        $document = new DOMDocument();
        $document->appendChild($document->importNode($element, true));
        return new Document($document->saveXML());
    }
}