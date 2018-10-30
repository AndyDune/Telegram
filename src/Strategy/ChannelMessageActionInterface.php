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


namespace AndyDune\WebTelegram\Strategy;
use AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages;

interface ChannelMessageActionInterface
{
    public function executeAction(ChannelMessages $document);
}