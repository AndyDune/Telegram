<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 21.07.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\Request;


class RequestChannelMessage extends RequestBase
{
    protected $uriTemplate = 'https://t.me/#channel#/#id#?embed=1';

    protected $channelName;

    /**
     * @return mixed
     */
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * @param mixed $channelName
     * @return $this
     */
    public function setChannelName($channelName): RequestChannelMessage
    {
        $this->channelName = $channelName;
        return $this;
    }



    public function retrieveMessage($id)
    {
        $data = [
            'id' => $id,
            'channel' => $this->getChannelName()
        ];
        return $this->execute($data);
    }
}