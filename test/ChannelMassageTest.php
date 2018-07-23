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
use AndyDune\WebTelegram\ExtractFromHtml\ChannelMessage;
use AndyDune\WebTelegram\Request\RequestChannelMessage;
use PHPUnit\Framework\TestCase;

class ChannelMassageTest extends TestCase
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

    public function testExtract()
    {
        $message = new ChannelMessage(file_get_contents(__DIR__ . '/data/message/good.html'));
        $this->assertTrue($message->isSuccess());
        $this->assertEquals('78', $message->getMessageViews());
        $this->assertEquals('2017-06-22T05:38:32+00:00', $message->getMessageDate());
        $this->assertEquals('<b>How she longed to get out of that dark hall</b> — слова <b>how</b> и <b>what</b> используются в начале восклицательных предложений: <i>Как же ей хотелось выбраться из этого темного холла</i>.<br><br><b>Why, there’s hardly enough of me</b> — <b>why</b> в начале предложения, выделенное запятой, — это восклицание, выражающее удивление. Переводится словами «ну как же», «в самом деле» и т.д.: <i>В самом деле, от меня едва ли..</i>.<br><br><b>enough</b> — достаточно;<br/><b>respectable</b> — приличный<br/><br><i>Why, there’s hardly enough of me left to make ONE respectable person&</i>',
            $message->getMessageBody());

        $message = new ChannelMessage(file_get_contents(__DIR__ . '/data/message/good.html'));
        $this->assertTrue($message->isSuccess());
        $this->assertEquals('78', $message->getMessageViews());
        $this->assertEquals('2017-06-22T05:38:32+00:00', $message->getMessageDate());
        $this->assertEquals('Как дела?',
            $message->getMessageBody());


        $message = new ChannelMessage(file_get_contents(__DIR__ . '/data/message/no_find.html'));
        $this->assertFalse($message->isSuccess());
        $this->assertEquals(null, $message->getMessageViews());
        $this->assertEquals(ChannelMessage::ERROR_POST_NOT_FOUND, $message->getErrorCode());
        $this->assertEquals('Post not found', $message->getErrorMassage());

    }


}