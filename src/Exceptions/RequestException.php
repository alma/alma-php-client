<?php

namespace Alma\API\Exceptions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestException extends AlmaException
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

    /**
     * @return mixed|string
     */
    public function getErrorMessage()
    {
        $message = $this->getMessage();

        if ($message) {
            return $message;
        }

        if (isset($this->response->json['errors'][0]['message'])
        ) {
            return $this->response->json['errors'][0]['message'];
        }

        return '';
    }
}
