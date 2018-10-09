<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 30.07.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\DoctrineOdm\Facade;


use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessagesVersions;
use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelsInfoForMessages;
use AndyDune\WebTelegram\ExtractFromHtml\ChannelMessage;
use AndyDune\WebTelegram\Format\NormalizeName;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

class ChannelMessages
{
    use NormalizeName;
    /**
     * @var DocumentManager
     */
    private $documentManager = null;

    /**
     * @var ChannelsInfoForMessages
     */
    private $channelInfoDocument;

    /**
     * ChannelMessages constructor.
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @param $name
     * @param bool $addOnNotExist
     * @return $this
     */
    public function retrieveWithName($name, $addOnNotExist = true)
    {
        $this->channelInfoDocument = null;
        $document = $this->getChannelInfoRepository()->findOneBy(['name' => $this->normalizeName($name)]);
        if (!$document and $addOnNotExist) {
            $document = new ChannelsInfoForMessages();
            $document->populateForNew();
            $document->setName($name);
            $this->documentManager->persist($document);
        }
        $this->channelInfoDocument = $document;
        return $this;
    }

    public function deleteChannelWithName($name)
    {
        /** @var DocumentRepository  $repo */
        $repo = $this->getChannelInfoRepository();
        /** @var ChannelsInfoForMessages  $document */
        $document = $repo->findOneBy(['name' => $this->normalizeName($name)]);
        if (!$document) {
            return false;
        }
        $repo->getDocumentManager()->remove($document);
        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repo */
        $repo = $this->getChannelMessagesRepository();
        /**
         * arrray[
         * ok => 1
         * n = 3
         * err = null
         * ermsg = null
         * ]
         */
        $result = $repo->deleteChannel($document);
        $repo->getDocumentManager()->flush();
        return true;
    }


    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getChannelInfoRepository()
    {
        return $this->documentManager->getRepository(ChannelsInfoForMessages::class);
    }

    /**
     * @return \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages
     */
    public function getChannelMessagesRepository()
    {
        return $this->documentManager->getRepository(\AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages::class);
    }

    public function getLastMessages($limit = 20, $noDeleted = false)
    {
        if (!$this->channelInfoDocument) {
            throw new \Exception('Вызовите сначала метод retrieveWithName');
        }
        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $this->documentManager
            ->getRepository(\AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages::class);
        $repository->setSortByDate(-1)->setLimit($limit);
        if ($noDeleted) {
            return $repository->findMessagesOfChannelNotDeleted($this->channelInfoDocument);
        }
        return $repository->findMessagesOfChannel($this->channelInfoDocument);
    }

    public function getChannelInfoDocument()
    {
        return $this->channelInfoDocument;
    }

    public function fillMessageInstanceWithExtractedData(ChannelMessage $extractor)
    {
        if (!$extractor->isSuccess()) {
            return false;
        }

        $channelMessage = $this->getMessageWithId($extractor->getId());

        $channelMessage->setViews($extractor->getMessageViews());
        $channelMessage->setDate($extractor->getMessageDate());

        if ($content = $extractor->getMessageBody()) {
            $channelMessage->setText($content);
        }

        if ($content = $extractor->getMessagePhotoLink()) {
            $pathInfo = parse_url($content);
            if (isset($pathInfo['host'])) {
                $channelMessage->setWidgetMessagePhotoLink($pathInfo['host']);
            } else {
                $channelMessage->setWidgetMessagePhotoLink('temp_link');
            }
        }

        if ($content = $extractor->getMessageSticker()) {
            $channelMessage->setWidgetMessageSticker($content);
        }

        if ($content = $extractor->getMessageVoice()) {
            $pathInfo = parse_url($content);
            if (isset($pathInfo['host'])) {
                $channelMessage->setWidgetMessageVoice($pathInfo['host']);
            } else {
                $channelMessage->setWidgetMessageVoice('temp_link');
            }
        }

        $this->getChannelMessagesRepository()->getDocumentManager()->flush();
        return true;
    }

    /**
     * @param $id
     * @param bool $addOnNotExist
     * @return \AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages
     * @throws \Exception
     */
    public function getMessageWithId($id, $addOnNotExist = true)
    {
        if (!$this->channelInfoDocument) {
            throw new \Exception('Вызовите сначала метод retrieveWithName');
        }
        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $this->documentManager
            ->getRepository(\AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages::class);
        $message = $repository->getMessageOfChannel($this->channelInfoDocument, $id);
        if (!$message and $addOnNotExist) {
            $message = new \AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages();
            $message->populateForNew();
            $message->setChannel($this->channelInfoDocument)->setIdWithinChannel($id);
            $this->documentManager->persist($message);
        }
        return $message;

    }

    public function flush()
    {
        $this->documentManager->flush();
    }
}