<?php

namespace Alma\API\Tests\Unit\Lib;

use Alma\API\Exceptions\ParametersException;
use Alma\API\Lib\PaymentValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class PaymentValidatorTest extends MockeryTestCase
{
    public static function purchaseAmountProvider(): array
    {
        return [
            'valid_integer_amount' => [
                ['payment' => ['purchase_amount' => 1000]],
                true
            ],
            'missing_purchase_amount' => [
                ['payment' => []],
                true
            ],
            'null_purchase_amount' => [
                ['payment' => ['purchase_amount' => null]],
                true
            ],
            'non_integer_amount' => [
                ['payment' => ['purchase_amount' => '1000']],
                ParametersException::class
            ],
        ];
    }

    /**
     * Ensure checkPurchaseAmount handles various scenarios
     * @dataProvider purchaseAmountProvider
     * @param array $data
     * @param mixed $expected
     * @return void
     * @throws ParametersException
     */
    public function testCheckPurchaseAmountHandlesScenarios(array $data, $expected)
    {
        if (is_string($expected) && class_exists($expected)) {
            $this->expectException($expected);
        }

        $result = PaymentValidator::checkPurchaseAmount($data);

        if ($expected === true) {
            $this->assertTrue($result);
        }
    }
}