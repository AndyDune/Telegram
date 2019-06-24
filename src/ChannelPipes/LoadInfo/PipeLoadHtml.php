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

use GuzzleHttp\Client;

class PipeLoadHtml
{

    public function __invoke(Data $data, callable $next, $type = null, $typePriority = null, $allowDelete = true)
    {
        try {
            $client = new Client([
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7)',
                ]
            ]);
            $res = $client->request('GET', $this->getUrl($data->getChannelName()), ['allow_redirects' => false]);
        } catch (\Exception $e) {
            $data->setErrorMessage($e->getMessage());
            $data->setErrorCode($e->getCode());
            $data->setErrorPlace('html_request');
            return $data;
        }

        $code = $res->getStatusCode();
        $data->setStatusCode($code);

        $headers = $res->getHeaders();
        $data->setHeaders($headers);

        $body = $res->getBody();
        $html = $body->getContents();
        $data->setHtmlBody($html);

        if ($code != 200) {
            $data->setErrorMessage('Status is not 200');
            $data->setErrorCode(0);
            $data->setErrorPlace('html_status_code_not_200');
            return $data;
        }

        return $next($data);
    }

    protected function getUrl($channelName)
    {
        return 'https://t.me/s/' . $channelName;
    }
}