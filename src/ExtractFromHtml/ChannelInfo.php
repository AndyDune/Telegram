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
use AndyDune\WebTelegram\ExtractFromHtml\Part\ExtractWithDomTrait;
use Zend\Dom\Document;

class ChannelInfo
{
    use ExtractWithDomTrait;

    protected $html;

    protected $participantsCount = null;

    protected $type = null;

    protected $messageDate = null;
    protected $messageViews = null;
    protected $messagePhotoLink = null;

    protected $success = false;

    protected $errorCode = null;
    protected $errorMassage = null;

    const ERROR_BAD_DATA_FORMAT = 100;
    const ERROR_EXCEPTION = 200;
    const ERROR_NO_PARTICIPANTS_COUNT = 101;
    const ERROR_UNKNOWN = 399;

    const TYPE_CHANNEL = 'channel';
    const TYPE_GROUP = 'group';
    const TYPE_PERSON = 'person';
    const TYPE_ = 'person';

    protected $tagPathForParticipantsCount = 'div.tgme_page_wrap div.tgme_page_extra';

    protected $tagPathForViewChannel = 'div.tgme_page_action a.tgme_action_button_new';

    protected $tagHeadTitle = 'head meta[property=og:title]';

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
            $this->doc = $doc;


            $res = Document\Query::execute($this->tagPathForParticipantsCount, $doc, Document\Query::TYPE_CSS);
            if ($res->count()) {
                /** @var \DOMNodeList $content */
                $content = current($res);
                $string = $content->item(0)->nodeValue;
                $parts = explode(',', $string); // 7 members, 2 online
                $string = $parts[0];
                $this->participantsCount = (int)preg_replace('|[^0-9]|iu', '', $string);
            }

            $this->extractType($doc, $html);

        } catch (\Exception $e) {
            $this->errorCode = self::ERROR_EXCEPTION;
            $this->errorMassage = $e->getCode(). ' : '. $e->getMessage();
            return false;
        }
        return true;
    }


    protected function extractType(Document $doc, $html)
    {
        $path = $this->extractAttribute('.tgme_page_context_btn .tgme_action_button_new', 'href');
        $string = $this->extractContentAsString('.tgme_page_context_btn .tgme_action_button_label');
        if ($path and strpos($path, '/s/') === 0 and preg_match('|Preview channel|ui', $string)) {
            $this->type = self::TYPE_CHANNEL;
            return;
        }

        $string = $this->extractContentAsString($this->tagPathForViewChannel);
        if (preg_match('|View Channel|ui', $string)) {
            $this->type = self::TYPE_CHANNEL;
            return;
        }

        $string = $this->extractContentAsString('.tgme_page_post .tgme_page_title');
        $stringExtra = $this->extractContentAsString('.tgme_page_post .tgme_page_extra');
        if ($string and $stringExtra and preg_match('|[\d]+ members|ui', $stringExtra)) {
            $this->type = self::TYPE_GROUP;
            return;
        }

            // channel is accessed in public
        $res = Document\Query::execute($this->tagPathForViewChannel, $doc, Document\Query::TYPE_CSS);
        if ($res->count()) {
            /** @var \DOMNodeList $content */
            $content = current($res);
            $string = $content->item(0)->nodeValue;
            if (preg_match('|View Channel|ui', $string)) {
                $this->type = self::TYPE_CHANNEL;
            }

            if (preg_match('|View Group|ui', $string)) {
                $this->type = self::TYPE_GROUP;
            }

            if (preg_match('|Send Message|ui', $string)) {

                $arr = [];

                $result1 = preg_match('|<meta property="og:title" content="([^"]+)"|ui', $html, $arr);

                $result2 = preg_match('|<meta property="og:image" content="https://telegram.org/img/t_logo.png">|ui',
                    $html);

                if (!$result2 and $result1 == 1 and !preg_match('|^Telegram: Contact @|ui', $arr[1])) {
                    $this->type = self::TYPE_PERSON;
                }

            }


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

    /**
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
    }



}