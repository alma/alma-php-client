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
     * @return string
     */
    public function getErrorMessage(): string
    {
        $message = $this->getMessage();
        if (!empty($message)) {
            return $message;
        }

        if (!empty($this->response->getReasonPhrase())) {
            return $this->response->getReasonPhrase();
        }

        return '';
    }
}
