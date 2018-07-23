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

use GuzzleHttp\Client;
use AndyDune\StringReplace\PowerReplace;

class RequestBase
{
    protected $uriTemplate;

    protected $responseCode = null;
    protected $responseContentType = null;
    protected $responseBody = null;
    protected $requestException = null;

    public function buildUri($data = [], $uri = null)
    {
        if (!$uri) {
            $uri = $this->getUriTemplate();
        }

        if (!$data) {
            return $uri;
        }

        $builder = new PowerReplace();
        $builder->setArray($data);
        return $builder->replace($uri);
    }

    /**
     * @return mixed
     */
    public function getUriTemplate()
    {
        return $this->uriTemplate;
    }

    /**
     * @param mixed $uriTemplate
     * @return $this
     */
    public function setUriTemplate($uriTemplate): RequestAbstract
    {
        $this->uriTemplate = $uriTemplate;
        return $this;
    }


    public function execute($data)
    {
        try {
            $client = new Client();
            $res = $client->request('GET', $this->buildUri($data));
            $this->requestException = null;
            $this->responseCode = $res->getStatusCode(); // 200
            $this->responseContentType = $res->getHeaderLine('content-type'); // application/xml; charset=windows-1251
            $this->responseBody = $res->getBody()->getContents(); // xml body
            return true;

        } catch (\Exception $e) {

            /*
            $message = $e->getMessage();
             * 0:cURL error 6: Could not resolve host: telegram.me (see http://curl.haxx.se/libcurl/c/libcurl-errors.html)
            if (preg_match('|Could not resolve host|', $message)) {
                $this->errorRetrieveDataFormTelegram = true;
            } else if (preg_match('|Server error|ui', $message)) {
                $this->errorRetrieveDataFormTelegram = true;
            }             *
             */

            $this->responseCode = null;
            $this->responseContentType = null;
            $this->responseBody = null;
            $this->requestException = $e;
        }
        return false;
    }


    /**
     * @return \Exception|null
     */
    public function getRequestException()
    {
        return $this->requestException;
    }

    /**
     * @return null|int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return null|string
     */
    public function getResponseContentType()
    {
        return $this->responseContentType;
    }

    /**
     * @return null|string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }
}