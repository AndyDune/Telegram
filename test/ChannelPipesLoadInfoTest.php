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
}