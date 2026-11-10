<?php

namespace Alma\Client\Application;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface extends PsrResponseInterface
{

    public function isError(): bool;
}
