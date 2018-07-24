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


/** @ODM\Document(collection="channel_messages") */
class ChannelMessages
{
    /** @ODM\Id */
    private $id = '';

    /** @ODM\Field(type="string") */
    private $channelName = '';

    /** @ODM\Field(type="date") */
    private $date;

    /** @ODM\Field(type="date") */
    private $dateLoaded;

    /** @ODM\Field(type="bool") */
    private $deleted = false;

    /** @ODM\Field(type="string") */
    private $text = '';

    /** @ODM\Field(type="string") */
    private $widgetMessagePhotoLink = '';

    /** @ODM\Field(type="int") */
    private $views = 0;

}