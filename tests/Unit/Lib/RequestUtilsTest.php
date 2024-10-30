<?php

namespace Alma\API\Tests\Unit\Lib;

use Alma\API\Lib\RequestUtils;
use PHPUnit\Framework\TestCase;
use stdClass;

class RequestUtilsTest extends TestCase
{
    public function testValidateRequestSignature()
    {
        $data = 'merchant_id_test';
        $apiKey = 'api_key_test';
        $signature = '0dd3cb4632c074ead0d0f346c75015c76ad4e1e115f01c7e0850dd5accb7b4b0';

        $this->assertTrue(RequestUtils::isHmacValidated($data, $apiKey,  $signature));
    }
    /**
     * @dataProvider checkHmacInvalidDataProvider
     * @param $data
     * @param $apiKey
     * @param $signature
     * @return void
     */
    public function testHmacDataDifferentFromSignature($data, $apiKey,  $signature)
    {
        $this->assertFalse(RequestUtils::isHmacValidated($data, $apiKey,  $signature));
    }

    public static function checkHmacInvalidDataProvider()
    {
        return [
            'String data' => [
                'data' => 'payment_id_test',
                'apiKey' => 'api_key_test',
                'signature' => 'wrong_signature'
            ],
            'Empty array data' => [
                'data' => [],
                'apiKey' => 'api_key_test',
                'signature' => 'wrong_signature'
            ],
            'Empty array apiKey' => [
                'data' => 'payment_id_test',
                'apiKey' => [],
                'signature' => 'wrong_signature'
            ],
            'Empty array signature' => [
                'data' => 'payment_id_test',
                'apiKey' => 'api_key_test',
                'signature' => []
            ],
            'Empty string data' => [
                'data' => '',
                'apiKey' => 'api_key_test',
                'signature' => 'wrong_signature'
            ],
            'Empty string apiKey' => [
                'data' => 'payment_id_test',
                'apiKey' => '',
                'signature' => 'wrong_signature'
            ],
            'Empty string signature' => [
                'data' => 'payment_id_test',
                'apiKey' => 'api_key_test',
                'signature' => ''
            ],
            'Object data' => [
                'data' => new stdClass(),
                'apiKey' => 'api_key_test',
                'signature' => 'wrong_signature'
            ],
            'Object apiKey' => [
                'data' => 'payment_id_test',
                'apiKey' => new stdClass(),
                'signature' => 'wrong_signature'
            ],
            'Object signature' => [
                'data' => 'payment_id_test',
                'apiKey' => 'api_key_test',
                'signature' => new stdClass()
            ],
            'Boolean data' => [
                'data' => false,
                'apiKey' => 'api_key_test',
                'signature' => 'wrong_signature'
            ],
            'Boolean apiKey' => [
                'data' => 'payment_id_test',
                'apiKey' => true,
                'signature' => 'wrong_signature'
            ],
            'Boolean signature' => [
                'data' => 'payment_id_test',
                'apiKey' => 'api_key_test',
                'signature' => true
            ],
            'Int data' => [
                'data' => 1,
                'apiKey' => 'api_key_test',
                'signature' => 'wrong_signature'
            ],
            'Int apiKey' => [
                'data' => 'payment_id_test',
                'apiKey' => 2,
                'signature' => 'wrong_signature'
            ],
            'Int signature' => [
                'data' => 'payment_id_test',
                'apiKey' => 'api_key_test',
                'signature' => 3
            ]

        ];
    }

}
