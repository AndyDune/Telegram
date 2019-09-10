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


namespace AndyDuneTest\WebTelegram;

use AndyDune\Pipeline\Pipeline;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\Data;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\PipeCheckDataBeforeParse;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\PipeExtractChannelInfo;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\PipeExtractChannelInfoBriefPage;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\PipeExtractChannelMessages;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\PipeLoadHtml;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\PipeLoadHtmlBriefPage;
use AndyDune\WebTelegram\Registry;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\TestCase;


class ChannelPipesLoadInfoBriefPageTest extends TestCase
{
    /**
     * @covers PipeLoadHtmlBriefPage
     */
    public function testLoadHtml()
    {
        $name = 'chat_msk';

        $data = new Data();
        $data->setChannelName($name);

        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeLoadHtmlBriefPage::class);
        $pipeLine->execute();

        $this->assertEquals(200, $data->getStatusCode());


        $name = 'chat_msk_not_exists';
        $data = new Data();
        $data->setChannelName($name);

        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeLoadHtmlBriefPage::class);
        $pipeLine->execute();

        $this->assertEquals(200, $data->getStatusCode());
    }

    public function testCheckDataBeforeParse()
    {
        $data = new Data();
        $data->setStatusCode(200);
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info/chat_msk.html'));
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeCheckDataBeforeParse::class);
        $pipeLine->pipe(function ($data, $next) {
            return 'good';
        });
        $result = $pipeLine->execute();
        $this->assertEquals('good', $result);
    }

    public function testPipeExtractChannelInfo()
    {
        $data = new Data();
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info/chat_msk.html'));
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeExtractChannelInfoBriefPage::class);
        /** @var Data $result */
        $result = $pipeLine->execute();

        $this->assertEquals('Чат "Моя Москва"', $result->getChannelTitle());
        $this->assertEquals('Москва и Область от 18 лет.<br/><br/>Если оскорбляют или спам - жми: /report<br/><br/>ℹ Создатель: <a href="https://t.me/Xander_Ne0">@Xander_Ne0</a><br/>Канал: <a href="https://t.me/mskcity">@mskcity</a><br/>Наш сайт - https://лайк.москва<br/><br/>Еще чаты: <a href="https://t.me/chats_tg">@chats_tg</a><br/><br/>❌Запрещено:<br/>1. Реклама без разрешения;<br/>2. Порно;<br/>3. Наркотики;<br/>4. Оскорбления;<br/>5. Флуд.', $result->getChannelDescription());
        $this->assertEquals('https://cdn4.telesco.pe/file/l-oEuOwOx8UZ14lZCuzTfwcco6Y4I--FoIVP7Sr3Cm4xBxrlqHFkfKc8z5inHFhI2n4_jkAnetUVpxqQuftH56GBzrFQ9YVZVEBZHd0tcCVVlcMwSjvjDt-aiIOMKDQ0rR4Ie8B9ZjU9-gFfoq_BQZYwzX34e-IeIfXWM64MuTKnqoi1u2BLBbIRLmhnEMLC4LEiRt5E1NtcDTcM5mNg3SyCq8bBBdIuAh5Xwy8yWN53WjR6_PnZ0jHbniBin9uzZvKM5t1IGsIGHnOEQEMvxrfVT3C5bUoQMVsbhNiYtM9vXQE_GDAQtlv7L6OhVFDBd1mvllxaWSZ_uMUJfmijPg.jpg',
            $result->getChannelImageUrl());

        $this->assertEquals(2, $result->getChannelCountData());
        $this->assertEquals(2383, $result->getChannelMemberCount());
        $this->assertEquals(248, $result->getChannelMemberOnlineCount()); // это сигнализирует что это чат - не канал

        $data = new Data();
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info/chat_1.html'));
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeExtractChannelInfoBriefPage::class);
        /** @var Data $result */
        $result = $pipeLine->execute();
        $this->assertEquals(2, $result->getChannelCountData());

        $data = new Data();
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info/normal.html'));
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeExtractChannelInfoBriefPage::class);
        /** @var Data $result */
        $result = $pipeLine->execute();
        $this->assertEquals(1, $result->getChannelCountData());

        $data = new Data();
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info/no_data.html'));
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeExtractChannelInfoBriefPage::class);
        /** @var Data $result */
        $result = $pipeLine->execute();
        $this->assertEquals(Data::ERROR_CONTENT_NO_CHANNEL_TITLE, $result->getErrorCode());
        $this->assertEquals(0, $result->getChannelCountData());

        $data = new Data();
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info/person_1.html'));
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeExtractChannelInfoBriefPage::class);
        /** @var Data $result */
        $result = $pipeLine->execute();
        $this->assertEquals(Data::ERROR_CONTENT_PROBABLY_PERSON, $result->getErrorCode());
        $this->assertEquals(0, $result->getChannelCountData());
        $this->assertEquals('Андрей Рыжов', $result->getPersonTitle());
        $this->assertEquals('@andydune', $result->getPersonExtra());

        $data = new Data();
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info/private_1.html'));
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeExtractChannelInfoBriefPage::class);
        /** @var Data $result */
        $result = $pipeLine->execute();
        $this->assertEquals(Data::ERROR_CONTENT_NO_CHANNEL_TITLE, $result->getErrorCode());
        $this->assertEquals(0, $result->getChannelCountData());
    }

    public function testExtractFullFromSite()
    {
        $data = new Data();
        $data->setChannelName('rzn1rzn');
        $pipeLine = new Pipeline();
        $pipeLine->pipe(PipeLoadHtmlBriefPage::class, null, 46);
        $pipeLine->pipe(PipeCheckDataBeforeParse::class);
        $pipeLine->pipe(PipeExtractChannelInfoBriefPage::class);
        $pipeLine->send($data);
        $pipeLine->execute();
        $this->assertEquals('', $data->getErrorMessage());
        $this->assertEquals(200, $data->getStatusCode());
        $this->assertEquals(1, $data->getChannelCountData());
        $this->assertEquals('1rzn', $data->getChannelTitle());
        $this->assertEquals('Рязань номер один', $data->getChannelDescription());
        $this->assertTrue(strlen($data->getChannelImageUrl()) > 20);
        $this->assertEquals(null, $data->getChannelFileCount());
        $this->assertGreaterThan(1, $data->getChannelMemberCount());

        $data = new Data();
        $data->setChannelName('rzn1rzn123');
        $pipeLine = new Pipeline();
        $pipeLine->pipe(PipeLoadHtmlBriefPage::class, null, 46);
        $pipeLine->pipe(PipeCheckDataBeforeParse::class);
        $pipeLine->pipe(PipeExtractChannelInfoBriefPage::class);
        $pipeLine->send($data);
        $pipeLine->execute();
        $this->assertEquals(Data::ERROR_CONTENT_NO_CHANNEL_TITLE, $data->getErrorCode());
        $this->assertEquals(200, $data->getStatusCode());

        $data = new Data();
        $data->setChannelName('andydune');
        $pipeLine = new Pipeline();
        $pipeLine->pipe(PipeLoadHtmlBriefPage::class, null, 46);
        $pipeLine->pipe(PipeCheckDataBeforeParse::class);
        $pipeLine->pipe(PipeExtractChannelInfoBriefPage::class);
        $pipeLine->send($data);
        $pipeLine->execute();
        $this->assertEquals(Data::ERROR_CONTENT_PROBABLY_PERSON, $data->getErrorCode());
        $this->assertEquals(200, $data->getStatusCode());
        $this->assertEquals('Андрей Рыжов', $data->getPersonTitle());
        $this->assertEquals('@andydune', $data->getPersonExtra());


    }

}