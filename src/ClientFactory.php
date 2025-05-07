<?php

namespace Alma\API;

use Alma\API\Exceptions\MissingKeyException;
use Alma\API\Exceptions\ParametersException;

class ClientFactory
{
    private static $instance;

    private function __construct()
    {
        // Constructor is intentionally empty
    }

    /**
     * @throws ParametersException
     */
    public static function create($apiKey, $options = array()): ClientInterface
    {
        if (!self::$instance) {
            self::$instance = new Client($apiKey, $options);
        }
        return self::$instance;
    }
}
