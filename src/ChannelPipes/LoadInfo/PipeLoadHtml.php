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
    /**
     *
     * https://t.me/s/dune_english?before=34
     *
     * @var null|int
     */
    protected $before = null;


    public function __invoke(Data $data, callable $next, $before = null)
    {
        if (!$data->getChannelName()) {
            $data->setErrorMessage('No channel name was set.');
            $data->setErrorPlace(PipeLoadHtml::class);
            return $data;
        }

        if ($before) {
            $this->before = $before;
        }

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

        $data->setBeforeId($this->before);

        $code = $res->getStatusCode();
        $data->setStatusCode($code);

        $headers = $res->getHeaders();
        $data->setHeaders($headers);

        $body = $res->getBody();
        $html = $body->getContents();
        $data->setHtmlBody($html);

        return $next($data);
    }

    /**
     * @return int|null
     */
    public function getBefore(): ?int
    {
        return $this->before;
    }

    /**
     * @param int|null $before
     */
    public function setBefore(?int $before): void
    {
        $this->before = $before;
    }


    protected function getUrl($channelName)
    {
        $path = 'https://t.me/s/' . $channelName;
        if ($this->before) {
            $path .= "?before=" . $this->before;
        }
        return $path;
    }
}