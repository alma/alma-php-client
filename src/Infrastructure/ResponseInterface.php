<?php

namespace Alma\API\Infrastructure;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

interface ResponseInterface extends PsrResponseInterface
{

    public function isError(): bool;
}
