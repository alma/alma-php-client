<?php

namespace Alma\API\Tests\Integration;

use Alma\API\Domain\ValueObject\Environment;
use Alma\API\Infrastructure\ClientConfiguration;
use Alma\API\Infrastructure\CurlClient;

class ClientTestHelper
{
    public static function getAlmaClient(): CurlClient
    {
        $environment = new Environment(Environment::TEST_MODE);
        $config = new ClientConfiguration($_ENV['ALMA_API_KEY'], $environment);
        return new CurlClient($config);
    }
}