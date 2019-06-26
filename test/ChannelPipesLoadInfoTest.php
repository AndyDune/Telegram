<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 24.06.2019                            |
 * -----------------------------------------------
 *
 */


namespace AndyDuneTest\WebTelegram;


use AndyDune\Pipeline\Pipeline;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\Data;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\DataChannelMessage;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\PipeCheckDataBeforeParse;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\PipeExtractChannelInfo;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\PipeExtractChannelMessages;
use AndyDune\WebTelegram\ChannelPipes\LoadInfo\PipeLoadHtml;
use PHPUnit\Framework\TestCase;

class ChannelPipesLoadInfoTest extends TestCase
{
    public function testLoadHtml()
    {
        $name = 'dune_english';

        $data = new Data();
        $data->setChannelName($name);

        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeLoadHtml::class);
        $pipeLine->execute();

        $this->assertEquals(200, $data->getStatusCode());


        $name = 'dune_english_not_exists';
        $data = new Data();
        $data->setChannelName($name);

        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeLoadHtml::class);
        $pipeLine->execute();

        $this->assertEquals(302, $data->getStatusCode());
    }

    public function testCheckDataBeforeParse()
    {
        $data = new Data();
        $data->setStatusCode(200);
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeCheckDataBeforeParse::class);
        $pipeLine->pipe(function ($data, $next) {
            return 'good';
        });
        $result = $pipeLine->execute();
        $this->assertInstanceOf(Data::class, $result);
        $this->assertEquals('No html doctype.', $data->getErrorMessage());


        $data = new Data();
        $data->setStatusCode(0);
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeCheckDataBeforeParse::class);
        $pipeLine->pipe(function ($data, $next) {
            return 'good';
        });
        $result = $pipeLine->execute();
        $this->assertInstanceOf(Data::class, $result);
        $this->assertEquals('Status is not 200.', $data->getErrorMessage());


        $data = new Data();
        $data->setStatusCode(302);
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeCheckDataBeforeParse::class);
        $pipeLine->pipe(function ($data, $next) {
            return 'good';
        });
        $result = $pipeLine->execute();
        $this->assertInstanceOf(Data::class, $result);
        $this->assertEquals('Was redirect 302.', $data->getErrorMessage());


        $data = new Data();
        $data->setStatusCode(200);
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info_s/dune_english.html'));
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
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info_s/dune_english.html'));
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeExtractChannelInfo::class);
        /** @var Data $result */
        $result = $pipeLine->execute();

        $this->assertEquals('По английски', $result->getChannelTitle());
        $this->assertEquals('Читаю книги на английском и публикую отдельные фразы, слова. С переводом, а иногда с анализом и озвучкой.', $result->getChannelDescription());
        $this->assertEquals('https://cdn4.telesco.pe/file/VlxCX7Eb4q1aGof_6zHIHA3SNFsiV3HbU287Iwj5nMnY07UznCr2S8E9ladXQA0fFbLACN-XoTcALNOTGUgRE0PqY3MQqyu2LJOeyGeozDRowMjlH6nNMMUdEy9H6K5ewgkh7QDyX2Oj1NpWB-K-iynToiyNr5AhEQwoiNI06Om9mxECRwC_IzQHBW0ufynZTW5-sLyyrFaTF2fTCoj-BmJwkCsCcXWl5MW9uOmNRGuptY9iUAYCVeR-ZizguIfBoQ85yxpIAbhsF9NkI2w7oMbGTJGUlyLjtjmBjvApOgGlxrnOgdGKCeh2dZsg0oJ8Yxe1N1WnGy6Gy8nF76oJ2g.jpg',
            $result->getChannelImageUrl());

        $this->assertEquals(3, $result->getChannelCountData());
        $this->assertEquals(13, $result->getChannelMemberCount());
        $this->assertEquals(2, $result->getChannelPhotoCount());
        $this->assertEquals(10, $result->getChannelLinkCount());
    }

    public function testPipeExtractChannelMessages()
    {
        $data = new Data();
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info_s/dune_english.html'));
        $pipeLine = new Pipeline();
        $pipeLine->send($data);
        $pipeLine->pipe(PipeExtractChannelMessages::class);
        /** @var Data $result */
        $result = $pipeLine->execute();
        $this->assertInstanceOf(DataChannelMessage::class, current($result->getMessages()));
        $message = current($result->getMessages());
        $this->assertEquals(54, $message->getId());
        $this->assertEquals('Use this words instead very:<br/><br/>very good -&gt; superb<br/>very hungry -&gt; ravenous<br/>very rude -&gt; vulgar<br/>very short -&gt; brief<br/>very boring -&gt; dull<br/>very hot -&gt; scalding (scorching)<br/>veru fast -&gt; rapid<br/>very tired -&gt; exhausted<br/>very poor -&gt; destitute<br/>very rich -&gt; wealthy',
            $message->getText());
        $this->assertEquals('2019-01-22 12:01:34', $message->getDateTime()->format('Y-m-d H:i:s'));



        //$string = file_get_contents('https://t.me/s/rzn1rzn?before=46'); // 46 port is not included
        //file_put_contents(__DIR__ . '/data/channel_info_s/rzn1rzn_before_46.html', $string);
    }

    public function testExtractFullFromFile()
    {
        $data = new Data();
        $data->setHtmlBody(file_get_contents(__DIR__ . '/data/channel_info_s/rzn1rzn_before_46.html'));
        $data->setStatusCode(200);

        $pipeLine = new Pipeline();
        $pipeLine->pipe(PipeCheckDataBeforeParse::class);
        $pipeLine->pipe(PipeExtractChannelInfo::class);
        $pipeLine->pipe(PipeExtractChannelMessages::class);
        $pipeLine->send($data);

        $pipeLine->execute();
        $this->assertEquals(1, $data->getChannelFileCount());

        $messages = $data->getMessages();

        // Message with file
        /** @var DataChannelMessage $message */
        $message = array_pop($messages);
        $this->assertEquals(45, $message->getId());
        $this->assertEquals('destiny.tgz', $message->getDocumentTitle());
        $this->assertEquals('646 KB', $message->getDocumentExtra());
        $this->assertEquals('Архив с картинкой', $message->getText());
        $this->assertEquals('2019-06-26 10:36:46', $message->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(2, $message->getViewsCount());

        // Message with text
        /** @var DataChannelMessage $message */
        $message = array_pop($messages);
        $this->assertEquals(44, $message->getId());
        $this->assertEquals(null, $message->getDocumentTitle());
        $this->assertEquals(null, $message->getDocumentExtra());
        $this->assertEquals('неходите января<br/>скажем девять — говоря<br/>выступает Левый Фланг<br/>— это просто не хорошо. —<br/>и панг.', $message->getText());
        $this->assertEquals('2019-06-26 10:35:42', $message->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(2, $message->getViewsCount());

        // Message with emoji
        /** @var DataChannelMessage $message */
        $message = array_pop($messages);
        $this->assertEquals(43, $message->getId());
        $this->assertEquals(null, $message->getDocumentTitle());
        $this->assertEquals(null, $message->getDocumentExtra());
        $this->assertEquals('<i class="emoji" style="background-image:url(\'//telegram.org/img/emoji/40/F09F9880.png\')"><b>😀</b></i>', $message->getText());
        $this->assertEquals('2019-06-26 10:35:27', $message->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(2, $message->getViewsCount());

        // Message with sticker
        /** @var DataChannelMessage $message */
        $message = array_pop($messages);
        $this->assertEquals(42, $message->getId());
        $this->assertEquals(null, $message->getDocumentTitle());
        $this->assertEquals(null, $message->getDocumentExtra());
        $this->assertEquals('', $message->getText());
        $this->assertEquals('2019-06-26 10:35:09', $message->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(2, $message->getViewsCount());
        $this->assertEquals('https://cdn4.telesco.pe/file/6158981d74.webp?token=XB2-RI9mlEDQLIk1fyn0VCJxb6N1uanlJPTAw5Oc1zOb9IkVb-gdFaClEc3UsFnLySwtIx0HUuPrWNuXV1RvClW7i9ZZry9nKaiUr9jRQem4HxWJAlXeYC12mW9GB5z-FfBUkLc0xV8dmLgpB96yHwens_qjHCuKK7K926N73Va8IavSeVVwf_DSdFe-z-miCugIDHXwRcxpRSZKzjbJ1v1exVbj8m5_E5VsLFHms6_3ZE4PsIR2c_DLgnHK8qIIjpAvDurAuCshe6cq9LjzbJotroQWInvuS0iXPQYR94OpIReLUQLevi1W_zWugNk6tQDhMACUcOmyD4_5yrkWwQ',
            $message->getStickerImage());

        array_pop($messages);

        // Message with sticker
        /** @var DataChannelMessage $message */
        $message = array_pop($messages);
        $this->assertEquals(40, $message->getId());
        $this->assertEquals(null, $message->getDocumentTitle());
        $this->assertEquals(null, $message->getDocumentExtra());
        $this->assertEquals('', $message->getText());
        $this->assertEquals('2019-06-26 10:32:59', $message->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(2, $message->getViewsCount());
        $this->assertEquals('https://cdn4.telesco.pe/file/52e90a376d.ogg?token=j4bSVp8U2oS5KBmD_J2mxWFwOkmFVQpD-uucWes-EHEq63HjAJW0KrNVBFlh8z5R_hVzn9ab26AWa0lSVL6P9HuK67IjoIaSoQVX_aW5r7jgde66pAAHR7yD7T9RbVtAfCIxOrERgMD5lbIBNMGDNAall2gMlJFVY2NdwVqFvQyJ_66KGMPCSf6QobZP6kk9uUxtwIVEKOBVMN5TpTQu6rdSLBEfAJar3QD-imQv0eT55FOF8gPJcOroHQE0mSrTgtmkKOBqlCtSafswp3oizIG5rRYp9vCYq5lQgKtLTIB7B21WLu3K3gB-5dU6EGlrGixmSzc3YMJTUW1i2WE58Q',
            $message->getMessageVoice());

    }

    public function testExtractFullFromSite()
    {
        $data = new Data();
        $data->setChannelName('rzn1rzn');
        $pipeLine = new Pipeline();
        $pipeLine->pipe(PipeLoadHtml::class, null,46);
        $pipeLine->pipe(PipeCheckDataBeforeParse::class);
        $pipeLine->pipe(PipeExtractChannelInfo::class);
        $pipeLine->pipe(PipeExtractChannelMessages::class);
        $pipeLine->send($data);

        $pipeLine->execute();

        $this->assertEquals('', $data->getErrorMessage());
        $this->assertEquals(200, $data->getStatusCode());

        $this->assertGreaterThan(0, $data->getChannelFileCount());
        $this->assertGreaterThan(1, $data->getChannelMemberCount());
        $this->assertGreaterThan(1, $data->getChannelLinkCount());
        $this->assertGreaterThan(1, $data->getChannelPhotoCount());


        $messages = $data->getMessages();

        // Message with file
        /** @var DataChannelMessage $message */
        $message = array_pop($messages);
        $this->assertEquals(45, $message->getId());
        $this->assertEquals('destiny.tgz', $message->getDocumentTitle());
        $this->assertEquals('646 KB', $message->getDocumentExtra());
        $this->assertEquals('Архив с картинкой', $message->getText());
        $this->assertEquals('2019-06-26 10:36:46', $message->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertGreaterThan(1, $message->getViewsCount());

        // Message with text
        /** @var DataChannelMessage $message */
        $message = array_pop($messages);
        $this->assertEquals(44, $message->getId());
        $this->assertEquals(null, $message->getDocumentTitle());
        $this->assertEquals(null, $message->getDocumentExtra());
        $this->assertEquals('неходите января<br/>скажем девять — говоря<br/>выступает Левый Фланг<br/>— это просто не хорошо. —<br/>и панг.', $message->getText());
        $this->assertEquals('2019-06-26 10:35:42', $message->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertGreaterThan(1, $message->getViewsCount());

        // Message with emoji
        /** @var DataChannelMessage $message */
        $message = array_pop($messages);
        $this->assertEquals(43, $message->getId());
        $this->assertEquals(null, $message->getDocumentTitle());
        $this->assertEquals(null, $message->getDocumentExtra());
        $this->assertEquals('<i class="emoji" style="background-image:url(\'//telegram.org/img/emoji/40/F09F9880.png\')"><b>😀</b></i>', $message->getText());
        $this->assertEquals('2019-06-26 10:35:27', $message->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertGreaterThan(1, $message->getViewsCount());

        // Message with sticker
        /** @var DataChannelMessage $message */
        $message = array_pop($messages);
        $this->assertEquals(42, $message->getId());
        $this->assertEquals(null, $message->getDocumentTitle());
        $this->assertEquals(null, $message->getDocumentExtra());
        $this->assertEquals('', $message->getText());
        $this->assertEquals('2019-06-26 10:35:09', $message->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertGreaterThan(1, $message->getViewsCount());
        $this->assertTrue(strlen($message->getStickerImage()) > 10);

        array_pop($messages);

        // Message with sticker
        /** @var DataChannelMessage $message */
        $message = array_pop($messages);
        $this->assertEquals(40, $message->getId());
        $this->assertEquals(null, $message->getDocumentTitle());
        $this->assertEquals(null, $message->getDocumentExtra());
        $this->assertEquals('', $message->getText());
        $this->assertEquals('2019-06-26 10:32:59', $message->getDateTime()->format('Y-m-d H:i:s'));
        $this->assertGreaterThan(1, $message->getViewsCount());
        $this->assertTrue(strlen($message->getMessageVoice()) > 10);

    }

}