<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 21.07.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDuneTest\WebTelegram;
use AndyDune\WebTelegram\Request\RequestChannelMessage;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testRequest()
    {
        $name = 'dune_english';
        $id = '31';

        $request = new RequestChannelMessage();
        $result = $request->setChannelName($name)
            ->retrieveMessage($id);

        $body = $request->getResponseBody();
        $this->assertTrue((bool)preg_match('|<title>Telegram Widget</title>|ui', $body));
        $this->assertTrue((bool)preg_match('|tgme_widget_message_text|ui', $body));
        $this->assertTrue((bool)preg_match('|tgme_widget_message_author|ui', $body));
        $this->assertTrue((bool)preg_match('|How she longed to get out of that dark hall|ui', $body));
    }
}