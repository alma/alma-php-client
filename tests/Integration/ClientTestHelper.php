<?php

namespace Alma\API\Tests\Integration;

use Alma\API\Infrastructure\ClientConfiguration;
use Alma\API\Infrastructure\CurlClient;

class ClientTestHelper
{
    public static function getAlmaClient(): CurlClient
    {
        $config = new ClientConfiguration($_ENV['ALMA_API_KEY'], Environment::TEST_MODE,);
        return new CurlClient($config);
    }
}