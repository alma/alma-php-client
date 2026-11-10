<?php

namespace Alma\Client\Tests\Integration;

use Alma\Client\Application\ClientConfiguration;
use Alma\Client\Application\CurlClient;
use Alma\Client\Domain\ValueObject\Environment;

class ClientTestHelper
{
    public static function getAlmaClient(): CurlClient
    {
        $environment = new Environment(Environment::TEST_MODE);
        $config = new ClientConfiguration($_ENV['ALMA_API_KEY'], $environment);
        return new CurlClient($config);
    }
}