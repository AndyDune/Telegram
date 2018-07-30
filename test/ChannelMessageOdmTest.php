<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 28.07.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDuneTest\WebTelegram;
use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelsInfoForMessages;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelMessage;
use AndyDune\WebTelegram\Registry;
use AndyDune\WebTelegram\Request\RequestChannelMessage;
use PHPUnit\Framework\TestCase;


class ChannelMessageOdmTest extends TestCase
{
    public function testChannelInfo()
    {
        $registry = Registry::getInstance();
        $dm = $registry->getServiceManager()->get('document_manager');

        /** @var ChannelsInfoForMessages $infoChannel */
        $infoChannel = $registry->getServiceManager()->get(ChannelsInfoForMessages::class);
        $infoChannel->populateForNew();
        $infoChannel->setName('test_dune_ENGLISH');

        $dm->flush();
    }
}