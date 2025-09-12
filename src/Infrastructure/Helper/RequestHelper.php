<?php

namespace Alma\API\Infrastructure\Helper;

class RequestHelper
{
    /**
     * Validate the HMAC signature of the request
     *
     * @param string $data
     * @param string $apiKey
     * @param string $signature
     * @return bool
     */
    public static function isHmacValidated(string $data, string $apiKey, string $signature): bool
    {
        return hash_hmac('sha256', $data, $apiKey) === $signature;
    }
}
