<?php

namespace Alma\API\Lib;

class RequestUtils
{
    /**
     * Validate the HMAC signature of the request
     *
     * @param string $data
     * @param string $apiKey
     * @param string $signature
     * @return bool
     */
    public static function isHmacValidated($data, $apiKey,  $signature)
    {
        return is_string($data) &&
            is_string($apiKey) &&
            hash_hmac('sha256', $data, $apiKey) === $signature;
    }

}