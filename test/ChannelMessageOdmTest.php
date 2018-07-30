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

use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages;
use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelsInfoForMessages;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelMessage;
use AndyDune\WebTelegram\Registry;
use AndyDune\WebTelegram\Request\RequestChannelMessage;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\TestCase;


class ChannelMessageOdmTest extends TestCase
{
    public function testChannelInfo()
    {
        $registry = Registry::getInstance();
        /** @var DocumentManager $dm */
        $dm = $registry->getServiceManager()->get('document_manager');

        $base = $dm->getDocumentDatabase(ChannelsInfoForMessages::class)->selectCollection('channel_info_for_messages');
        $base->remove(['name' => ['$in' => ['test_dune_english', 'test_rzn1rzn']]]);

        $baseMessages = $dm->getDocumentDatabase(ChannelsInfoForMessages::class)->selectCollection('channel_messages');
        $baseMessages->remove(['channelName' => ['$in' => ['test_dune_english', 'test_rzn1rzn']]]);

        $this->assertEquals(0, $base->count(['name' => 'test_dune_english']));

        /** @var ChannelsInfoForMessages $infoChannel */
        $infoChannel = $registry->getServiceManager()->get(ChannelsInfoForMessages::class);
        $infoChannel->populateForNew();
        $infoChannel->setName('test_dune_ENGLISH');

        /** @var ChannelsInfoForMessages $infoChannel */
        $infoChannelRzn = $registry->getServiceManager()->get(ChannelsInfoForMessages::class);
        $infoChannelRzn->populateForNew();
        $infoChannelRzn->setName('test_rzn1rzn');

        $dm->flush();

        $this->assertEquals(1, $base->count(['name' => 'test_dune_english']));
        $this->assertEquals(1, $base->count(['name' => 'test_rzn1rzn']));

        /** @var ChannelMessages $message */
        $message = $registry->getServiceManager()->get(ChannelMessages::class);
        $this->assertTrue(strlen($message->getId()) > 10); // У несохраненной записи уже есть id
        $message->setChannel($infoChannel);
        $message->setIdWithinChannel(12)
            ->setText('Привет все');

        $dm->flush();

        $this->assertEquals(1, $baseMessages->count(['channelName' => 'test_dune_english']));

        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $dm->getRepository(ChannelMessages::class);
        $results = $repository->findMessagesOfChannel($infoChannel);
        $this->assertCount(1, $results);


        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $dm->getRepository(ChannelMessages::class);
        $results = $repository->getMessageOfChannel($infoChannel, 12);
        $this->assertInstanceOf(ChannelMessages::class, $results);
        $this->assertEquals('Привет все', $results->getText());
        $this->assertEquals('test_dune_english', $results->getChannelName());

        $dm->clear();


        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $dm->getRepository(ChannelMessages::class);
        $results = $repository->getMessageOfChannelWithName('test_dune_english', 12);
        $this->assertInstanceOf(ChannelMessages::class, $results);
        $this->assertEquals('Привет все', $results->getText());
        $this->assertEquals('test_dune_english', $results->getChannelName());


    }

    public function testChannelInfoFacade()
    {
        $registry = Registry::getInstance();
        /** @var DocumentManager $dm */
        $dm = $registry->getServiceManager()->get('document_manager');

        $base = $dm->getDocumentDatabase(ChannelsInfoForMessages::class)->selectCollection('channel_info_for_messages');
        $base->remove(['name' => ['$in' => ['test_dune_english', 'test_rzn1rzn', 'test_test']]]);

        $baseMessages = $dm->getDocumentDatabase(ChannelsInfoForMessages::class)->selectCollection('channel_messages');
        $baseMessages->remove(['channelName' => ['$in' => ['test_dune_english', 'test_rzn1rzn', 'test_test']]]);

        $facade = new \AndyDune\WebTelegram\DoctrineOdm\Facade\ChannelMessages($dm);

        $this->assertEquals(null, $facade->retrieveWithName('test_test', false)->getChannelInfoDocument());

        $this->assertEquals('test_test', $facade->retrieveWithName('test_test')->getChannelInfoDocument()->getName());

        $this->assertEquals(null, $facade->getMessageWithId(11, false));


        $dm->flush();
    }
}