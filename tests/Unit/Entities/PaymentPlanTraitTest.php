<?php

namespace Alma\API\Tests\Unit\Entities;

use Alma\API\Entities\PaymentPlanTrait;
use Alma\API\Exceptions\Endpoint\EligibilityEndpointException;
use PHPUnit\Framework\TestCase;

class PaymentPlanTraitTest extends TestCase
{
    public static function paymentPlanProvider(): array
    {
        return [
            'pnx' => [
                'params' => [
                    'kind' => 'general',
                    'installments_count' => 4,
                    'deferred_days' => 0,
                    'deferred_months' => 0,
                ],
                'expected' => [
                    'plan_key' => 'general_4_0_0',
                    'is_pay_later_only' => false,
                    'is_pnx_only' => true,
                    'is_both_pnx_and_pay_later' => false,
                    'is_pay_now' => false,
                ]
            ],
            'paylater_deferred_months' => [
                'params' => [
                    'kind' => 'general',
                    'installments_count' => 1,
                    'deferred_days' => 0,
                    'deferred_months' => 3,
                ],
                'expected' => [
                    'plan_key' => 'general_1_0_3',
                    'is_pay_later_only' => true,
                    'is_pnx_only' => false,
                    'is_both_pnx_and_pay_later' => false,
                    'is_pay_now' => false,
                ]
            ],
            'paylater_deferred_days' => [
                'params' => [
                    'kind' => 'general',
                    'installments_count' => 1,
                    'deferred_days' => 30,
                    'deferred_months' => 0,
                ],
                'expected' => [
                    'plan_key' => 'general_1_30_0',
                    'is_pay_later_only' => true,
                    'is_pnx_only' => false,
                    'is_both_pnx_and_pay_later' => false,
                    'is_pay_now' => false,
                ]
            ],
            'pnx_and_paylater' => [
                'params' => [
                    'kind' => 'general',
                    'installments_count' => 3,
                    'deferred_days' => 15,
                    'deferred_months' => 0,
                ],
                'expected' => [
                    'plan_key' => 'general_3_15_0',
                    'is_pay_later_only' => false,
                    'is_pnx_only' => false,
                    'is_both_pnx_and_pay_later' => true,
                    'is_pay_now' => false,
                ]
            ],
            'pay_now' => [
                'params' => [
                    'kind' => 'general',
                    'installments_count' => 1,
                    'deferred_days' => 0,
                    'deferred_months' => 0,
                ],
                'expected' => [
                    'plan_key' => 'general_1_0_0',
                    'is_pay_later_only' => false,
                    'is_pnx_only' => false,
                    'is_both_pnx_and_pay_later' => false,
                    'is_pay_now' => true,
                ]
            ]
        ];
    }

    /**
     * @dataProvider paymentPlanProvider
     * @param array $params
     * @param array $expected
     * @return void
     * @throws EligibilityEndpointException
     */
    public function testPaymentPlanTrait(array $params, array $expected): void
    {
        // Implement the PaymentPlanTrait in an anonymous class
        $paymentPlanImplementation = new class () {
            use PaymentPlanTrait;

            private array $params;

            public function setParams($params) {
                $this->params = $params;
            }
            public function getKind(): string
            {
                return $this->params['kind'];
            }

            public function getInstallmentsCount(): int
            {
                return $this->params['installments_count'];
            }

            public function getDeferredDays(): int
            {
                return $this->params['deferred_days'];
            }

            public function getDeferredMonths(): int
            {
                return $this->params['deferred_months'];
            }
        };

        // Inject the data
        $paymentPlanImplementation->setParams($params);

        // Test the trait
        $this->assertEquals($expected['plan_key'], $paymentPlanImplementation->getPlanKey());
        $this->assertEquals($expected['is_pay_later_only'], $paymentPlanImplementation->isPayLaterOnly());
        $this->assertEquals($expected['is_pnx_only'], $paymentPlanImplementation->isPnXOnly());
        $this->assertEquals($expected['is_both_pnx_and_pay_later'], $paymentPlanImplementation->isBothPnxAndPayLater());
        $this->assertEquals($expected['is_pay_now'], $paymentPlanImplementation->isPayNow());
    }
}
