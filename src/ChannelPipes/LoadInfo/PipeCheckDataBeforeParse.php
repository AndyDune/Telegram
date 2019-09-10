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


class PipeCheckDataBeforeParse
{
    /**
     * @var callable|null
     */
    protected $callBackOnProblem = null;

    public function __invoke(Data $data, callable $next)
    {
        if ($data->getStatusCode() == 302) {
            $data->setErrorMessage('Was redirect 302.');
            $data->setErrorCode(Data::ERROR_CODE_302);
            $data->setErrorPlace(PipeCheckDataBeforeParse::class);
            return $this->executeCallBack($data);
        }

        if ($data->getStatusCode() != 200) {
            $data->setErrorMessage('Status is not 200.');
            $data->setErrorCode(Data::ERROR_NO_200);
            $data->setErrorPlace(PipeCheckDataBeforeParse::class);
            return $this->executeCallBack($data);
        }

        if (!preg_match('|<!DOCTYPE html>|ui', $data->getHtmlBody())) {
            $data->setErrorMessage('No html doctype.');
            $data->setErrorCode(Data::ERROR_NO_DOCTYPE);
            $data->setErrorPlace(PipeCheckDataBeforeParse::class);
            return $this->executeCallBack($data);
        }

        return $next($data);
    }

    public function executeCallBack(Data $data)
    {
        if ($this->callBackOnProblem and is_callable($this->callBackOnProblem)) {
            call_user_func($this->callBackOnProblem, $data);
        }
        return $data;
    }

    /**
     * @param callable $callBackOnProblem
     * @return PipeCheckDataBeforeParse
     */
    public function setCallBackOnProblem(callable $callBackOnProblem): self
    {
        $this->callBackOnProblem = $callBackOnProblem;
        return $this;
    }



}