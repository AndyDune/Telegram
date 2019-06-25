<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 24.06.2019                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\ChannelPipes\LoadInfo;

use AndyDune\WebTelegram\Format\NormalizeName;

class Data
{
    use NormalizeName;

    const ERROR_CODE_302 = '302';
    const ERROR_NO_200 = 'no_200';
    const ERROR_NO_DOCTYPE = 'no_doctype';
    const ERROR_NO_CHANNEL_TITLE = 'no_channel_title';

    protected $channelName = null;
    protected $channelNormalName = null;

    protected $htmlBody = '';
    protected $statusCode = null;
    protected $headers = null;

    protected $errorMessage = '';
    protected $errorCode = null;
    protected $errorPlace = null;

    protected $channelTitle = null;
    protected $channelDescription = null;
    protected $channelImageUrl = null;
    protected $channelMemberCount = null;
    protected $channelPhotoCount = null;
    protected $channelLinkCount = null;

    protected $channelCountData = 0;

    /**
     * @var DataChannelMessage[]
     */
    protected $messages = [];

    /**
     * @return null
     */
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * @param null $channelName
     */
    public function setChannelName($channelName): void
    {
        $this->channelNormalName = $this->normalizeName($channelName);
        $this->channelName = $channelName;
    }

    /**
     * @return string
     */
    public function getHtmlBody(): string
    {
        return $this->htmlBody;
    }

    /**
     * @param string $htmlBody
     */
    public function setHtmlBody(string $htmlBody): void
    {
        $this->htmlBody = $htmlBody;
    }

    /**
     * @return null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param null $statusCode
     */
    public function setStatusCode($statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $headers
     */
    public function setHeaders($headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param null $errorCode
     */
    public function setErrorCode($errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return null
     */
    public function getErrorPlace()
    {
        return $this->errorPlace;
    }

    /**
     * @param null $errorPlace
     */
    public function setErrorPlace($errorPlace): void
    {
        $this->errorPlace = $errorPlace;
    }

    /**
     * @return null
     */
    public function getChannelNormalName()
    {
        return $this->channelNormalName;
    }

    /**
     * @param null $channelNormalName
     */
    public function setChannelNormalName($channelNormalName): void
    {
        $this->channelNormalName = $channelNormalName;
    }

    /**
     * @return null
     */
    public function getChannelTitle()
    {
        return $this->channelTitle;
    }

    /**
     * @param null $channelTitle
     */
    public function setChannelTitle($channelTitle): void
    {
        $this->channelTitle = $channelTitle;
    }

    /**
     * @return null
     */
    public function getChannelDescription()
    {
        return $this->channelDescription;
    }

    /**
     * @param null $channelDescription
     */
    public function setChannelDescription($channelDescription): void
    {
        $this->channelDescription = $channelDescription;
    }

    /**
     * @return null
     */
    public function getChannelImageUrl()
    {
        return $this->channelImageUrl;
    }

    /**
     * @param null $channelImageUrl
     */
    public function setChannelImageUrl($channelImageUrl): void
    {
        $this->channelImageUrl = $channelImageUrl;
    }

    /**
     * @return null
     */
    public function getChannelMemberCount()
    {
        return $this->channelMemberCount;
    }

    /**
     * @param null $channelMemberCount
     */
    public function setChannelMemberCount($channelMemberCount): void
    {
        $this->channelMemberCount = $channelMemberCount;
    }

    /**
     * @return null
     */
    public function getChannelPhotoCount()
    {
        return $this->channelPhotoCount;
    }

    /**
     * @param null $channelPhotoCount
     */
    public function setChannelPhotoCount($channelPhotoCount): void
    {
        $this->channelPhotoCount = $channelPhotoCount;
    }

    /**
     * @return null
     */
    public function getChannelLinkCount()
    {
        return $this->channelLinkCount;
    }

    /**
     * @param null $channelLinkCount
     */
    public function setChannelLinkCount($channelLinkCount): void
    {
        $this->channelLinkCount = $channelLinkCount;
    }

    /**
     * @return int
     */
    public function getChannelCountData(): int
    {
        return $this->channelCountData;
    }

    /**
     * @param int $channelCountData
     */
    public function setChannelCountData(int $channelCountData): void
    {
        $this->channelCountData = $channelCountData;
    }

    /**
     * @return DataChannelMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param DataChannelMessage $message
     */
    public function addMessage(DataChannelMessage $message): void
    {
        $this->messages[] = $message;
    }


}