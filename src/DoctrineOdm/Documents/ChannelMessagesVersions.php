<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 04.10.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\DoctrineOdm\Documents;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class ChannelMessagesVersions
{
    /** @ODM\Field(type="int") */
    protected $findChannel;

    /** @ODM\Field(type="int") */
    protected $findSticker;

    /**
     * @return mixed
     */
    public function getFindChannel()
    {
        return $this->findChannel;
    }

    /**
     * @param mixed $findChannel
     * @return $this
     */
    public function setFindChannel($findChannel): ChannelMessagesVersions
    {
        $this->findChannel = $findChannel;
        return $this;
    }

    /**
     * @return int
     */
    public function getFindSticker()
    {
        return $this->findSticker;
    }

    /**
     * @param mixed $findSticker
     * @return $this
     */
    public function setFindSticker($findSticker): ChannelMessagesVersions
    {
        $this->findSticker = $findSticker;
        return $this;
    }

}