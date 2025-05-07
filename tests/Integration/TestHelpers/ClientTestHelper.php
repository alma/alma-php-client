<?php

namespace Alma\API\Tests\Integration\TestHelpers;

use Alma\API\Client;
use Alma\API\Exceptions\ParametersException;

class ClientTestHelper
{
    /**
     * @throws ParametersException
     */
    public static function getAlmaClient(): Client
    {
        return new Client(
            $_ENV['ALMA_API_KEY'],
            ['mode' => 'test', 'api_root' => $_ENV['ALMA_API_ROOT'], 'force_tls' => false]
        );
    }

}