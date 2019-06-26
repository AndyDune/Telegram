<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 25.06.2019                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\ChannelPipes\LoadInfo;
use DateTime;

class DataChannelMessage
{
    protected $id;
    protected $text = null;
    protected $dateTime = null;

    protected $documentTitle = null;
    protected $documentExtra = null;

    protected $viewsCount = null;

    /**
     * Link to sticker image
     * Can be changed
     *
     * @var null|string
     */
    protected $stickerImage = null;

    protected $messageVoice = null;


    /**
     * @param mixed $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }


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
     * @return null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param null $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return null|DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param mixed $date
     */
    public function setDateTime($date): void
    {
        $this->dateTime = $date;
    }

    /**
     * @return null
     */
    public function getDocumentTitle()
    {
        return $this->documentTitle;
    }

    /**
     * @param null $documentTitle
     */
    public function setDocumentTitle($documentTitle): void
    {
        $this->documentTitle = $documentTitle;
    }

    /**
     * @return null
     */
    public function getDocumentExtra()
    {
        return $this->documentExtra;
    }

    /**
     * @param null $documentExtra
     */
    public function setDocumentExtra($documentExtra): void
    {
        $this->documentExtra = $documentExtra;
    }

    /**
     * @return null
     */
    public function getViewsCount()
    {
        return $this->viewsCount;
    }

    /**
     * @param null $viewsCount
     */
    public function setViewsCount($viewsCount): void
    {
        $this->viewsCount = $viewsCount;
    }

    /**
     * @return string|null
     */
    public function getStickerImage(): ?string
    {
        return $this->stickerImage;
    }

    /**
     * @param string|null $stickerImage
     */
    public function setStickerImage(?string $stickerImage): void
    {
        $this->stickerImage = $stickerImage;
    }

    /**
     * @return null
     */
    public function getMessageVoice()
    {
        return $this->messageVoice;
    }

    /**
     * @param null $messageVoice
     */
    public function setMessageVoice($messageVoice): void
    {
        $this->messageVoice = $messageVoice;
    }

}