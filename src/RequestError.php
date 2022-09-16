<?php

namespace Alma\API;

use Alma\API\Exceptions\RequestException;

/**
 * Class RequestError
 *
 * @package Alma\API
 * @deprecated Use RequestException instead
 */
class RequestError extends RequestException
{
    /**
     * @var Request|null
     * @deprecated use getRequest() instead
     */
    public $request;

    /**
     * @var Response|null
     * @deprecated use getRequest() instead
     */
    public $response;
}
