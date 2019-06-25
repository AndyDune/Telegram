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
    }
}