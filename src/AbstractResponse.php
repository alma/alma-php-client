<?php

namespace Alma\API;

class AbstractResponse
{
    public $responseCode;
    public $json;
    public $responseFile;
    public $errorMessage;

    /**
     * @return mixed
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return mixed
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @return mixed
     */
    public function getResponseFile()
    {
        return $this->responseFile;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
