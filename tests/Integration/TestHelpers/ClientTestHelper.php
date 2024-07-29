<?php

namespace Alma\API\Tests\Integration\TestHelpers;

use Alma\API\Client;

class ClientTestHelper
{
    public static function getAlmaClient()
    {
        return new Client(
            $_ENV['ALMA_API_KEY'],
            ['mode' => 'test', 'api_root' => $_ENV['ALMA_API_ROOT'], 'force_tls' => false]
        );
    }

}