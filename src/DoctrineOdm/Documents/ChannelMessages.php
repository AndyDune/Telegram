<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 24.07.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\DoctrineOdm\Documents;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Indexes;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Index;
use phpDocumentor\Reflection\Types\This;


/** @ODM\Document(collection="channel_messages", repositoryClass="AndyDune\WebTelegram\DoctrineOdm\Repository\ChannelMessages")
    @Indexes({
        @Index(keys={"channel"="asc", "date"="desc"}),
        @Index(keys={"channel"="asc", "deleted"="asc", "date"="desc"}),
        @Index(keys={"channel"="asc", "date"="asc"}),
        @Index(keys={"channel"="asc", "idWithinChannel"="asc"})
      })
 */
class ChannelMessages
{
    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="int") */
    private $idWithinChannel;

    /** @ODM\Field(type="string") */
    private $channelName = '';


    /** @ODM\ReferenceOne(targetDocument="ChannelsInfoForMessages", storeAs="id") */
    private $channel;

    /** @ODM\Field(type="date") */
    private $date;

    /** @ODM\Field(type="date") */
    private $dateLoaded;

    /** @ODM\Field(type="date") */
    private $dateUpdated;

    /** @ODM\Field(type="int") */
    private $countUpdates;

    /** @ODM\Field(type="bool") */
    private $deleted = false;

    /** @ODM\Field(type="string") */
    private $text = '';

    /** @ODM\Field(type="string") */
    private $widgetMessagePhotoLink = '';

    /** @ODM\Field(type="int") */
    private $views = 0;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getIdWithinChannel()
    {
        return $this->idWithinChannel;
    }

    /**
     * @param mixed $idWithinChannel
     * @return ChannelMessages
     */
    public function setIdWithinChannel($idWithinChannel): ChannelMessages
    {
        $this->idWithinChannel = $idWithinChannel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * @param mixed $channelName
     */
    public function setChannelName($channelName): void
    {
        $this->channelName = $channelName;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param ChannelsInfoForMessages $channel
     * @return  ChannelMessages
     */
    public function setChannel(ChannelsInfoForMessages $channel): ChannelMessages
    {
        $this->channel = $channel;
        $this->setChannelName($channel->getName());
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return $this
     */
    public function setDate($date): ChannelMessages
    {
        if (is_string($date)) {
            try {
                $date = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $date);
            } catch (\Exception $e) {
                $date = new \DateTime();
            }
        }
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateLoaded()
    {
        return $this->dateLoaded;
    }

    /**
     * @param mixed $dateLoaded
     * @return $this
     */
    public function setDateLoaded($dateLoaded = null): ChannelMessages
    {
        if (!$dateLoaded) {
            $dateLoaded = new \DateTime();
        }
        $this->dateLoaded = $dateLoaded;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getWidgetMessagePhotoLink()
    {
        return $this->widgetMessagePhotoLink;
    }

    /**
     * @param mixed $widgetMessagePhotoLink
     */
    public function setWidgetMessagePhotoLink($widgetMessagePhotoLink): void
    {
        $this->widgetMessagePhotoLink = $widgetMessagePhotoLink;
    }

    /**
     * @return mixed
     */
    public function getViews()
    {
        return $this->views;
    }

    public function markUpdate()
    {
        $this->countUpdates = (int)$this->countUpdates + 1;
        $this->dateUpdated = new \DateTime();
        return $this;
    }

    /**
     * @param mixed $dateUpdated
     * @return $this
     */
    public function setDateUpdated($dateUpdated): ChannelMessages
    {
        if (is_string($dateUpdated)) {
            try {
                $dateUpdated = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $dateUpdated);
            } catch (\Exception $e) {
                $dateUpdated = new \DateTime();
            }
        }

        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    /**
     * @param mixed $countUpdates
     * @return $this
     */
    public function setCountUpdates($countUpdates): ChannelMessages
    {
        $this->countUpdates = $countUpdates;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @return mixed
     */
    public function getCountUpdates()
    {
        return $this->countUpdates;
    }


    /**
     * @param mixed $views
     * @return $this
     */
    public function setViews($views): ChannelMessages
    {
        $this->views = $views;
        return $this;
    }

    public function populateForNew()
    {
        $this->countUpdates = 0;
        $this->date = new \DateTime();
        $this->dateLoaded = new \DateTime();
        return $this;
    }

}