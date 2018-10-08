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

use AndyDune\DateTime\DateTime;
use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages;
use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelsInfoForMessages;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelMessage;
use AndyDune\WebTelegram\Registry;
use AndyDune\WebTelegram\Request\RequestChannelMessage;
use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\TestCase;


class ChannelMessageOdmTest extends TestCase
{
    public function testChannelInfo()
    {
        $registry = Registry::getInstance();
        /** @var DocumentManager $dm */
        $dm = $registry->getServiceManager()->get('document_manager');

        $dm->getSchemaManager()->ensureIndexes();

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
        $message->setIdWithinChannel(12)->setWidgetMessageVoice('ссылка на файл 12')
            ->setText('Привет все');

        $message = $registry->getServiceManager()->get(ChannelMessages::class);
        $this->assertTrue(strlen($message->getId()) > 10); // У несохраненной записи уже есть id
        $message->setChannel($infoChannel);
        $message->populateForNew();
        $message->setIdWithinChannel(13)
            ->setText('Привет все все')->setWidgetMessageVoice('ссылка на файл 13');

        $message = $registry->getServiceManager()->get(ChannelMessages::class);
        $this->assertTrue(strlen($message->getId()) > 10); // У несохраненной записи уже есть id
        $message->populateForNew();
        $message->setChannel($infoChannelRzn);
        $message->setIdWithinChannel(12)
            ->setText('Привет все все');

        $dm->flush();

        $this->assertEquals(2, $baseMessages->count(['channelName' => 'test_dune_english']));

        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $dm->getRepository(ChannelMessages::class);
        $results = $repository->findMessagesOfChannel($infoChannel);
        $this->assertCount(2, $results);

        $this->assertEquals(1, $baseMessages->count(['channelName' => 'test_rzn1rzn']));

        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $dm->getRepository(ChannelMessages::class);
        $results = $repository->findMessagesOfChannel($infoChannelRzn);
        $this->assertCount(1, $results);


        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $dm->getRepository(ChannelMessages::class);
        $results = $repository->getMessageOfChannel($infoChannel, 12);
        $this->assertInstanceOf(ChannelMessages::class, $results);
        $this->assertEquals('Привет все', $results->getText());
        $this->assertEquals('ссылка на файл 12', $results->getWidgetMessageVoice());
        $this->assertEquals('test_dune_english', $results->getChannelName());

        //$dm->clear();

        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $dm->getRepository(ChannelMessages::class);
        $results = $repository->getMessageOfChannelWithName('test_dune_english', 12);
        $this->assertInstanceOf(ChannelMessages::class, $results);
        $this->assertEquals('Привет все', $results->getText());
        $this->assertEquals('test_dune_english', $results->getChannelName());

        $qb = $dm->createQueryBuilder(ChannelMessages::class)
            ->field('channel')->equals($infoChannel)
            ->sort('date', 1);

        $qb->hint('channel_1_date_1');
        $query = $qb->getQuery();
        /** @var Cursor $result */
        $result = $query->execute();
        $result = $result->toArray();
        $this->assertCount(2, $result);
        $debug = $query->debug();

        $message = $registry->getServiceManager()->get(ChannelMessages::class);
        $message->populateForNew();
        $message->setChannel($infoChannelRzn);
        $message->setIdWithinChannel(123)
            ->setText('Имеет версии');

        $versions = $message->getVersions();
        $versions->setFindChannel(1);
        $versions->setFindSticker(3)->setUpdated(5);
        $dm->flush();

        $dm->clear();

        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $dm->getRepository(ChannelMessages::class);
        $message = $repository->getMessageOfChannelWithName('test_rzn1rzn', 123);
        $this->assertInstanceOf(ChannelMessages::class, $message);
        $versions = $message->getVersions();
        $this->assertEquals(1, $versions->getFindChannel());
        $this->assertEquals(3, $versions->getFindSticker());
        $this->assertEquals(5, $versions->getUpdated());
    }

    public function testChannelInfoFacade()
    {
        $registry = Registry::getInstance();
        /** @var DocumentManager $dm */
        $dm = $registry->getServiceManager()->get('document_manager');

        $base = $dm->getDocumentDatabase(ChannelsInfoForMessages::class)->selectCollection('channel_info_for_messages');
        $base->remove(['name' => ['$in' => ['test_dune_english', 'test_rzn1rzn', 'test_test']]]);

        $baseMessages = $dm->getDocumentDatabase(ChannelMessages::class)->selectCollection('channel_messages');
        $baseMessages->remove(['channelName' => ['$in' => ['test_dune_english', 'test_rzn1rzn', 'test_test']]]);

        $facade = new \AndyDune\WebTelegram\DoctrineOdm\Facade\ChannelMessages($dm);

        $this->assertEquals(null, $facade->retrieveWithName('test_test', false)->getChannelInfoDocument());

        $this->assertEquals([], $facade->retrieveWithName('test_test')->getLastMessages());
        $dm->flush();

        $this->assertEquals('test_test', $facade->retrieveWithName('test_test')->getChannelInfoDocument()->getName());

        $this->assertEquals(null, $facade->getMessageWithId(11, false));

        $message = $facade->getMessageWithId(11);
        $message->setText('Привет');
        $dm->flush();

        $this->assertCount(1, $messages = $facade->retrieveWithName('test_test', false)->getLastMessages());
        $this->assertEquals(11, current($messages)->getIdWithinChannel());


        $message = $facade->getMessageWithId(12);
        $message->setText('Привет 12');
        $message->setDate((new DateTime())->add('+ 12 minutes')->getValue());

        $message = $facade->getMessageWithId(13);
        $message->setText('Привет 13');
        $dm->flush();

        $this->assertCount(2, $messages = $facade->retrieveWithName('test_test', false)->getLastMessages(2));
        $this->assertEquals(12, current($messages)->getIdWithinChannel());


        $message = $facade->getMessageWithId(12);
        $message->setDate((new DateTime())->add('- 1 minutes')->getValue());
        $dm->flush();

        $messages = $facade->retrieveWithName('test_test', false)->getLastMessages(1);
        $this->assertCount(1, $messages);
        $oneMessage = current($messages);
        $this->assertEquals(13, $oneMessage->getIdWithinChannel());

        $oneMessage->setDeleted(true);
        $dm->flush();

        $messages = $facade->retrieveWithName('test_test', false)->getLastMessages(1);
        $this->assertCount(1, $messages);
        $oneMessage = current($messages);
        $this->assertEquals(13, $oneMessage->getIdWithinChannel());

        $messages = $facade->retrieveWithName('test_test', false)->getLastMessages(5, true);
        $this->assertCount(2, $messages);
        $oneMessage = current($messages);
        $this->assertEquals(11, $oneMessage->getIdWithinChannel());

        $oneMessage->setDeleted(true);
        $dm->flush();


        $messages = $facade->retrieveWithName('test_test', false)->getLastMessages(5, true);
        $this->assertCount(1, $messages);
        $oneMessage = current($messages);
        $this->assertEquals(12, $oneMessage->getIdWithinChannel());

    }

    public function testExtractDataAndSave()
    {
        $registry = Registry::getInstance();
        /** @var DocumentManager $dm */
        $dm = $registry->getServiceManager()->get('document_manager');
        $dm->clear();
        $dm->getSchemaManager()->ensureIndexes();

        $base = $dm->getDocumentDatabase(ChannelsInfoForMessages::class)->selectCollection('channel_info_for_messages');
        $base->remove(['name' => ['$in' => ['test_dune_english', 'test_rzn1rzn', 'test_test']]]);

        $baseMessages = $dm->getDocumentDatabase(ChannelMessages::class)->selectCollection('channel_messages');
        $baseMessages->remove(['channelName' => ['$in' => ['test_dune_english', 'test_rzn1rzn', 'test_test']]]);

        $message = new ChannelMessage(file_get_contents(__DIR__ . '/data/message/good.html'));

        $facade = new \AndyDune\WebTelegram\DoctrineOdm\Facade\ChannelMessages($dm);
        $facade->retrieveWithName('test_test');
        $facade->getChannelInfoDocument()->setLastDateLoadPost();

        $instance = $facade->getMessageWithId(133);
        $instance->setText($message->getMessageBody());
        $instance->setViews($message->getMessageViews());
        $instance->setDate($message->getMessageDate());
        $instance->setWidgetMessagePhotoLink($message->getMessagePhotoLink());
        $instance->setDateLoaded();

        $dm->flush();
        $dm->clear();

        $facade = new \AndyDune\WebTelegram\DoctrineOdm\Facade\ChannelMessages($dm);
        $instanceNew = $facade->retrieveWithName('test_test')->getMessageWithId(133);

        $this->assertEquals($instanceNew->getChannelName(), 'test_test');
        $this->assertEquals($instanceNew->getText(), $message->getMessageBody());
        $this->assertEquals($instanceNew->getViews(), $message->getMessageViews());
        $this->assertEquals($instanceNew->getWidgetMessagePhotoLink(), $message->getMessagePhotoLink());
        $this->assertEquals($instanceNew->getDate()->format('Y-m-d H:i:s'), $message->getMessageDate(true)->format('Y-m-d H:i:s'));

        /*
        $this->assertEquals(null, $facade->getChannelInfoDocument()->getDateInsert());
        if (!$facade->getChannelInfoDocument()->getDateInsert()) {
            $facade->getChannelInfoDocument()->setDateInsert(new \DateTime());
        }
        */

        $dm->flush();
        $dm->clear();


        $facade = new \AndyDune\WebTelegram\DoctrineOdm\Facade\ChannelMessages($dm);
        $facade->retrieveWithName('test_test');
        $this->assertInstanceOf(\DateTime::class, $facade->getChannelInfoDocument()->getDateInsert());

    }
}