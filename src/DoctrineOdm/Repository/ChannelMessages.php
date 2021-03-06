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


namespace AndyDune\WebTelegram\DoctrineOdm\Repository;
use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelsInfoForMessages;
use Doctrine\ODM\MongoDB\DocumentRepository;

class ChannelMessages extends DocumentRepository
{
    protected $sort = null;
    protected $limit = 100;
    protected $skip = 0;

    /**
     * @param int $order
     * @return $this
     */
    public function setSortByDate($order = 1)
    {
        $this->sort = ['date' => $order];
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int $skip
     * @return $this
     */
    public function setSkip($skip)
    {
        $this->skip = $skip;
        return $this;
    }

    /**
     * @param ChannelsInfoForMessages $channel
     * @param int $limit
     * @param int $skip
     * @return array
     */
    public function findMessagesOfChannel(ChannelsInfoForMessages $channel)
    {
        return $this->findBy(['channel' => $channel], $this->sort, $this->limit, $this->skip);
    }


    /**
     * @param ChannelsInfoForMessages $channel
     * @param int $limit
     * @param int $skip
     * @return array
     */
    public function findMessagesOfChannelNotDeleted(ChannelsInfoForMessages $channel)
    {
        return $this->findBy(['channel' => $channel, 'deleted' => false], $this->sort, $this->limit, $this->skip);
    }

    public function deleteChannel(ChannelsInfoForMessages $channel)
    {
        $queryBuilder =  $this->createQueryBuilder()->remove();
        return $queryBuilder->field('channel')->equals($channel)
            ->getQuery()
            ->execute();
    }


    public function deleteChannelMessageWithId(ChannelsInfoForMessages $channel, $id)
    {
        $queryBuilder =  $this->createQueryBuilder()->remove();
        return $queryBuilder->field('channel')->equals($channel)
            ->field('idWithinChannel')->equals($id)
            ->getQuery()
            ->execute();
    }


    /**
     * @param int $version
     * @param int $limit
     * @param bool $noDeleted
     * @return \AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages[]
     */
    public function getMessagesCheckVersionLessThen($version = 1, $limit = 10, $noDeleted = true)
    {
/*
        $queryBuilder =  $this->createQueryBuilder();
        return $queryBuilder->field('versions.checked')->lt($version)
            ->limit($limit)
            ->getQuery()
            ->execute()->toArray();
*/
        $query = ['$or' => [['versions.checked' => ['$lt' => $version]], ['versions.checked' => null]]];
        //$query = ['versions.checked' => ['$lt' => $version], 'versions.checked' => null];
        //$query = ['versions' => ['checked' => ['$lt' => $version]]];
        if ($noDeleted) {
            $query['deleted'] = false;
        }
        return $this->findBy($query, ['date' => 1], $limit);
    }


    /**
     * @param ChannelsInfoForMessages $channel
     * @param int $limit
     * @param int $skip
     * @return array
     */
    public function getMessageOfChannel(ChannelsInfoForMessages $channel, $id)
    {
        return $this->findOneBy(['channel' => $channel, 'idWithinChannel' => (int)$id]);
    }

    public function getMessageOfChannelWithName($name, $id)
    {
        return $this->findOneBy(['channelName' => $name, 'idWithinChannel' => (int)$id]);
    }

}