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


use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelsInfoForMessages;
use AndyDune\WebTelegram\Format\NormalizeName;
use Doctrine\ODM\MongoDB\DocumentManager;

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
        $document = $this->documentManager->getRepository(ChannelsInfoForMessages::class)->findOneBy(['name' => $this->normalizeName($name)]);
        if (!$document and $addOnNotExist) {
            $document = new ChannelsInfoForMessages();
            $document->setName($name);
            $this->documentManager->persist($document);
        }
        $this->channelInfoDocument = $document;
        return $this;
    }

    public function getLastMessages($limit = 20)
    {
        if (!$this->channelInfoDocument) {
            throw new \Exception('Вызовите сначала метод retrieveWithName');
        }
        /** @var \AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages $repository */
        $repository = $this->documentManager
            ->getRepository(\AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages::class);
        return $repository->setSortByDate(-1)->setLimit($limit)->findMessagesOfChannel($this->channelInfoDocument);
    }

    public function getChannelInfoDocument()
    {
        return $this->channelInfoDocument;
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