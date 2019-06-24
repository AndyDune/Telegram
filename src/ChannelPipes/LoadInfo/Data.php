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

    protected $channelName = null;
    protected $channelNormalName = null;

    protected $htmlBody = '';
    protected $statusCode = null;
    protected $headers = null;

    protected $errorMessage = '';
    protected $errorCode = null;
    protected $errorPlace = null;

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




}