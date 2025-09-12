<?php

namespace Alma\API\Infrastructure\Exception;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;

class ClientException extends Exception implements ClientExceptionInterface
{

}
