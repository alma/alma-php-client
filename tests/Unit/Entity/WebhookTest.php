<?php

namespace Alma\API\Tests\Unit\Entity;

use Alma\API\Helper\WebhookHelper;
use PHPUnit\Framework\TestCase;

class WebhookTest extends TestCase
{
    public static function verifySignatureHandlesScenariosProvider(): array
    {
        return [
            'valid_signature_with_url_encoding' => [
                'L1h5wQPg6UyaZlazatLpl_CtN7CwYSzxBIQ07u9DFjk',
                ['param1' => 'value1', 'param2' => 'value2'],
                'secret_key',
                true,
                true
            ],
            'invalid_signature_with_url_encoding' => [
                'invalid_signature',
                ['param1' => 'value1', 'param2' => 'value2'],
                'secret_key',
                true,
                false
            ],
            'valid_signature_without_url_encoding' => [
                'L1h5wQPg6UyaZlazatLpl_CtN7CwYSzxBIQ07u9DFjk',
                ['param1' => 'value1', 'param2' => 'value2'],
                'secret_key',
                false,
                true
            ],
            'invalid_signature_without_url_encoding' => [
                'invalid_signature',
                ['param1' => 'value1', 'param2' => 'value2'],
                'secret_key',
                false,
                false
            ],
            'empty_params' => [
                'L1h5wQPg6UyaZlazatLpl_CtN7CwYSzxBIQ07u9DFjk',
                [],
                'secret_key',
                true,
                false
            ],
        ];
    }

    /**
     * Ensure verifySignature handles various scenarios
     * @dataProvider verifySignatureHandlesScenariosProvider
     * @param string $signature
     * @param array $params
     * @param string $secret
     * @param bool $urlEncode
     * @param bool $expected
     * @return void
     */
    public function testSignatureHandlesScenarios(
        string $signature,
        array $params,
        string $secret,
        bool $urlEncode,
        bool $expected
    ) {
        $this->assertSame($expected, WebhookHelper::verifySignature($signature, $params, $secret, $urlEncode));
    }
}