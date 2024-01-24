<?php

namespace Alma\API\Exceptions;

use Alma\API\Request;
use Alma\API\Response;

class RequestException extends AlmaException
{
    /**
     * @var Request|null
     */
    public $request;
    /**
     * @var Response|null
     */
    public $response;

    /**
     * @param $message
     * @param $request
     * @param $response
     */
    public function __construct($message = "", $request = null, $response = null)
    {
        parent::__construct($message);

        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return mixed|string
     */
    public function getErrorMessage()
    {
        $message = $this->getMessage();

        if ($message) {
            return $message;
        }

        if (isset($this->response->json)
            && isset($this->response->json['errors'])
            && isset($this->response->json['errors'][0])
            && isset($this->response->json['errors'][0]['message'])
        ) {
            return $this->response->json['errors'][0]['message'];
        }

        return '';
    }
}