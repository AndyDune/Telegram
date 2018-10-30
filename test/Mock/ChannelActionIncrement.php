<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 30.10.2018                            |
 * -----------------------------------------------
 *
 */

namespace AndyDuneTest\WebTelegram\Mock;
use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages;
use AndyDune\WebTelegram\Strategy\ChannelMessageActionInterface;

class ChannelActionIncrement implements ChannelMessageActionInterface
{
    public function executeAction(ChannelMessages $document)
    {
        $count = (int)$document->getViews() + 1;
        $document->setViews($count);
        return $count;
    }
}