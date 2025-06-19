<?php

namespace Alma\API\Exceptions\Endpoint;

use Alma\API\Exceptions\AlmaException;
use Alma\API\ResponseInterface;
use Psr\Http\Message\RequestInterface;

abstract class AbstractEndpointServiceException extends AlmaException
{
    /**
     * @var RequestInterface|null
     */
    public ?RequestInterface $request;
    /**
     * @var ResponseInterface|null
     */
    public ?ResponseInterface $response;

    /**
     * @param string $message
     * @param RequestInterface|null $request
     * @param ResponseInterface|null $response
     */
    public function __construct(string $message = "", ?RequestInterface $request = null, ?ResponseInterface $response = null)
    {
        parent::__construct($message);

        $this->request = $request;
        $this->response = $response;
    }
}
