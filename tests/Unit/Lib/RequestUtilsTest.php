<?php

namespace Alma\API\Tests\Unit\Lib;

use Alma\API\Lib\RequestUtils;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

class RequestUtilsTest extends MockeryTestCase
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

    public static function checkHmacInvalidDataProvider(): array
    {
        return [
            'String data' => [
                'data' => 'payment_id_test',
                'apiKey' => 'api_key_test',
                'signature' => 'wrong_signature'
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
        ];
    }

}
