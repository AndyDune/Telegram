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
        $this->assertEquals('22.06.2017 05', $message->getMessageDate(true)->format('d.m.Y H'));
        $this->assertEquals('<b>How she longed to get out of that dark hall</b> — слова <b>how</b> и <b>what</b> используются в начале восклицательных предложений: <i>Как же ей хотелось выбраться из этого темного холла</i>.<br/><br/><b>Why, there’s hardly enough of me</b> — <b>why</b> в начале предложения, выделенное запятой, — это восклицание, выражающее удивление. Переводится словами «ну как же», «в самом деле» и т.д.: <i>В самом деле, от меня едва ли..</i>.<br/><br/><b>enough</b> — достаточно;<br/><b>respectable</b> — приличный<br/><br/><i>Why, there’s hardly enough of me left to make ONE respectable person!</i>',
            $message->getMessageBody());

        $message = new ChannelMessage(file_get_contents(__DIR__ . '/data/message/good_text.html'));
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


        $message = new ChannelMessage(file_get_contents(__DIR__ . '/data/message/good_photo.html'));
        $this->assertTrue($message->isSuccess());
        $this->assertEquals('214', $message->getMessageViews());
        $this->assertEquals('https://cdn4.telesco.pe/file/l4aVT53QTUsm48zkL1wJ33-ObWNMw7zBuz-usL-tGHoFOOAAahNFGVgBpJKVmvSG8_tqxfMRTXO9mFjnwgYuq05TjRylfj-ekdRPBtCeq15Hbvb48wxr2YiwsDOyaiZ698dNlR5f7b0Isc6K4_ZAL4Oh6OTvtReXNXTiN7t4mie8FrhP65med2tMjtGuj7JlqqLN3GYoAP9GPolmv21o_wHmGTmuq3i7-2ZIZrW_eCmcH5Mya5bMOX-5ooG7FbsUZHp2MlC-KL-kDS0F_rQV0MKekd2y7V5qbOs6nEEuRWDjp9vDO3W23mPBJVPq6s1xkHz2mxcpUhx7ChfzTeORlw.jpg',
            $message->getMessagePhotoLink());
        $this->assertEquals('',
            $message->getMessageBody());

        $message = new ChannelMessage(file_get_contents(__DIR__ . '/data/message/widget_message_voice.html'));
        $this->assertTrue($message->isSuccess());
        $this->assertEquals('https://cdn5.telesco.pe/file/nNTrF9bYwr8i1uSs3ZCyoTzmIAxVQ8rVuouLkW-d4FHcU7zf4WMrJJwjuQFDjLjmeFWh93t85mvVL-uX7K9PiREZEFwAbEf-5lJ5zwssYLvRE7lSgf_Hy-3rSYD8xdp-Lj9IdXE5uN788_uh_3YG8WTJQRewL2hqY3VjgYr7qw1rAdb6LrTOt-iCQu0CJg9HrJh69aI6eYo8JdS0txgVXxJfYJEuu5NId5RsVF5PgLAIEoWoL94aZX5BrYZUt7LqJKeBPhY6CrBkkdl_X0w9DAaTvTA4cPJWjUkOsW7c0-6J8fmTs-kpRavRaPOdU9L-ZrT-hH16-jQFj0UQFquOmg.ogg', $message->getMessageVoice());

    }


}