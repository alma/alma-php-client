<?php

namespace Alma\API;

use Exception;

class RequestError extends Exception
{
    /**
     * @var Request|null
     */
    public $request;
    /**
     * @var Response|null
     */
    public $response;

    public function __construct($message = "", $request = null, $response = null)
    {
        parent::__construct($message);

        $this->request = $request;
        $this->response = $response;
    }
}
