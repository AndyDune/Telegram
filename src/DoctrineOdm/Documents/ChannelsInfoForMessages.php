<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 24.07.2018                            |
 * -----------------------------------------------
 *
 * О индексах:
 * https://www.doctrine-project.org/projects/doctrine-mongodb-odm/en/1.2/reference/indexes.html#indexes
 */


namespace AndyDune\WebTelegram\DoctrineOdm\Documents;
use AndyDune\DateTime\DateTime;
use AndyDune\WebTelegram\Format\NormalizeName;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Index;


/** @ODM\Document(collection="channel_info_for_messages") */
class ChannelsInfoForMessages
{
    use NormalizeName;

    const STATUS_READY = 'ready';
    const STATUS_OFF = 'off';
    const STATUS_GO_TO_CHECK = 'to_check';

    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="string") @Index(unique=true, order="asc", name="name") */
    private $name = '';

    /** @ODM\Field(type="string") */
    private $status;

    /** @ODM\Field(type="int") */
    private $maxKnownPostId;

    /** @ODM\Field(type="int") */
    private $maxLoadedPostId;

    /** @ODM\Field(type="int") */
    private $minLoadedPostId;

    /** @ODM\Field(type="date") */
    private $lastDateCheckChannelExist;

    /** @ODM\Field(type="date") */
    private $lastDateLoadPost = null;

    /** @ODM\Field(type="date") */
    private $lastDateLoadPostNext;

    /** @ODM\Field(type="date") */
    private $lastDateLoadPostPrevious;

    /** @ODM\Field(type="date") */
    private $dateToCheckMessagesAfter;

    /** @ODM\Field(type="int") */
    private $postCount = 0;

    /** @ODM\Field(type="date") */
    private $dateToUpdateAfter = null;

    /** @ODM\Field(type="int") */
    private $countMessagesPerWeek = 0;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name): ChannelsInfoForMessages
    {
        $this->name = $this->normalizeName($name);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getMaxKnownPostId()
    {
        return $this->maxKnownPostId;
    }

    /**
     * @param mixed $maxKnownPostId
     * @return $this
     */
    public function setMaxKnownPostId($maxKnownPostId): ChannelsInfoForMessages
    {
        if (!$this->maxKnownPostId or $this->maxKnownPostId < $maxKnownPostId) {
            $this->maxKnownPostId = $maxKnownPostId;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxLoadedPostId()
    {
        return $this->maxLoadedPostId;
    }

    /**
     * @param mixed $maxLoadedPostId
     * @return  $this
     */
    public function setMaxLoadedPostId($maxLoadedPostId): ChannelsInfoForMessages
    {
        if (!$this->maxLoadedPostId or $this->maxLoadedPostId < $maxLoadedPostId) {
            $this->maxLoadedPostId = $maxLoadedPostId;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinLoadedPostId()
    {
        return $this->minLoadedPostId;
    }

    /**
     * @param mixed $minLoadedPostId
     * @return $this
     */
    public function setMinLoadedPostId($minLoadedPostId): ChannelsInfoForMessages
    {
        if (!$this->minLoadedPostId or $this->minLoadedPostId > $minLoadedPostId) {
            $this->minLoadedPostId = $minLoadedPostId;
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastDateCheckChannelExist()
    {
        return $this->lastDateCheckChannelExist;
    }

    /**
     * @param mixed $lastDateCheckChannelExist
     * @return $this
     */
    public function setLastDateCheckChannelExist($lastDateCheckChannelExist = null): ChannelsInfoForMessages
    {
        if (!$lastDateCheckChannelExist) {
            $lastDateCheckChannelExist = new \DateTime();
        }
        $this->lastDateCheckChannelExist = $lastDateCheckChannelExist;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastDateLoadPost()
    {
        return $this->lastDateLoadPost;
    }

    /**
     * @param mixed $lastDateLoadPost
     * @return  $this
     */
    public function setLastDateLoadPost($lastDateLoadPost = null): ChannelsInfoForMessages
    {
        if (!$lastDateLoadPost) {
            $lastDateLoadPost = new \DateTime();
        }

        $this->lastDateLoadPost = $lastDateLoadPost;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastDateLoadPostNext()
    {
        return $this->lastDateLoadPostNext;
    }

    /**
     * @param mixed $lastDateLoadPostNext
     */
    public function setLastDateLoadPostNext($lastDateLoadPostNext = null): ChannelsInfoForMessages
    {
        if (!$lastDateLoadPostNext) {
            $lastDateLoadPostNext = new \DateTime();
        }

        $this->lastDateLoadPostNext = $lastDateLoadPostNext;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastDateLoadPostPrevious()
    {
        return $this->lastDateLoadPostPrevious;
    }

    /**
     * @param mixed $lastDateLoadPostPrevious
     * @return $this
     */
    public function setLastDateLoadPostPrevious($lastDateLoadPostPrevious): ChannelsInfoForMessages
    {
        if (!$lastDateLoadPostPrevious) {
            $lastDateLoadPostPrevious = new \DateTime();
        }

        $this->lastDateLoadPostPrevious = $lastDateLoadPostPrevious;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostCount()
    {
        return $this->postCount;
    }

    /**
     * @param mixed $postCount
     */
    public function setPostCount($postCount): void
    {
        $this->postCount = $postCount;
    }

    /**
     * @return mixed
     */
    public function getDateToUpdateAfter()
    {
        return $this->dateToUpdateAfter;
    }

    /**
     * @param mixed $dateToUpdateAfter
     */
    public function setDateToUpdateAfter($dateToUpdateAfter): void
    {
        $this->dateToUpdateAfter = $dateToUpdateAfter;
    }

    /**
     * @return mixed
     */
    public function getDateToCheckMessagesAfter()
    {
        return $this->dateToCheckMessagesAfter;
    }

    /**
     * @param mixed $days
     * @return $this
     */
    public function addDaysToCheckMessagesAfter($days): ChannelsInfoForMessages
    {
        $date = new DateTime();
        $date->add(sprintf('+ %s days', $date));
        $this->dateToCheckMessagesAfter = $date->getValue();
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountMessagesPerWeek()
    {
        return $this->countMessagesPerWeek;
    }

    /**
     * @param mixed $countMessagesPerWeek
     * @return  $this
     */
    public function setCountMessagesPerWeek($countMessagesPerWeek): ChannelsInfoForMessages
    {
        $this->countMessagesPerWeek = $countMessagesPerWeek;
        return $this;
    }


    public function populateForNew()
    {
        $this->lastDateLoadPost = new \DateTime();
        $this->lastDateCheckChannelExist = new \DateTime();
        $this->dateToCheckMessagesAfter = new \DateTime();
        $this->status = self::STATUS_READY;
        $this->minLoadedPostId = 0;
        $this->maxLoadedPostId = 0;
        $this->maxKnownPostId = 0;
        return $this;
    }


}