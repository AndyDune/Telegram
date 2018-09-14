<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 14.09.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram\ExtractFromHtml;
use Zend\Dom\Document;

class ChannelInfo
{
    protected $html;

    protected $participantsCount = null;

    protected $messageDate = null;
    protected $messageViews = null;
    protected $messagePhotoLink = null;

    protected $success = false;

    protected $errorCode = null;
    protected $errorMassage = null;

    const ERROR_BAD_DATA_FORMAT = 100;
    const ERROR_EXCEPTION = 200;
    const ERROR_NO_PARTICIPANTS_COUNT = 101;


    const ERROR_POST_NOT_FOUND = 404;
    const ERROR_UNKNOWN = 399;

    protected $tagPathForParticipantsCount = 'div.tgme_page_wrap div.tgme_page_extra';

    public function __construct($html)
    {
        $this->html = $html;
        $this->success = $this->extract($html);
    }

    protected function extract($html)
    {

        try {
            if (!preg_match('| <title>Telegram: Contact|ui', $html)) {
                $this->errorCode = self::ERROR_BAD_DATA_FORMAT;
                $this->errorMassage = '';
                return false;
            }

            $doc = new Document($html);

            $res = Document\Query::execute($this->tagPathForParticipantsCount, $doc, Document\Query::TYPE_CSS);
            if ($res->count()) {
                /** @var \DOMNodeList $content */
                $content = current($res);
                $string = $content->item(0)->nodeValue;
                $this->participantsCount = (int)preg_replace('|[^0-9]|iu', '', $string);
            } else {
                $this->errorCode = self::ERROR_NO_PARTICIPANTS_COUNT;
                return false;
            }

        } catch (\Exception $e) {
            $this->errorCode = self::ERROR_EXCEPTION;
            $this->errorMassage = $e->getCode(). ' : '. $e->getMessage();
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }



    /**
     * @return null
     */
    public function getParticipantsCount()
    {
        return $this->participantsCount;
    }



    public function isFormatError()
    {
        return (in_array($this->errorCode,[self::ERROR_BAD_DATA_FORMAT, self::ERROR_EXCEPTION]));
    }

    public function isNoDataError()
    {
        return (in_array($this->errorCode,[self::ERROR_NO_PARTICIPANTS_COUNT]));
    }

    /**
     * @return null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return null
     */
    public function getErrorMassage()
    {
        return $this->errorMassage;
    }

}