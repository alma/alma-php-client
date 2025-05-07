<?php

namespace Alma\API\Tests\Integration\TestHelpers;

use Alma\API\Entities\Payment;
use Alma\API\Exceptions\ParametersException;

class PaymentTestHelper
{
    /**
     * @param int $amount
     * @param $installmentsCount
     * @return Payment
     * @throws ParametersException
     */
    public static function createPayment(int $amount, $installmentsCount): Payment
    {
        return ClientTestHelper::getAlmaClient()->payments->create(self::paymentData($amount, $installmentsCount));
    }

    private static function paymentData($amount, $installmentsCount = 3): array
    {
        return [
            'payment' => [
                'purchase_amount' => $amount,
                'installments_count' => $installmentsCount,
                'shipping_address' => [
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'line1' => '2 rue de la rue',
                    'city' => 'Paris',
                    'postal_code' => '75002',
                    'country' => 'FR',
                ]
            ],
            'customer' => [
                'first_name' => 'Test Integration',
                'last_name' => 'Ecom',
                'email' => 'test@almapay.com',
            ],
            'order' => [
                'merchant_reference' => 'ABC-123',
            ]
        ];
    }


}