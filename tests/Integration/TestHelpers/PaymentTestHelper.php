<?php

namespace Alma\API\Tests\Integration\TestHelpers;

use Alma\API\Entities\Payment;
use Alma\API\ParamsError;
use Alma\API\RequestError;

class PaymentTestHelper
{
    /**
     * @param int $amount
     * @return Payment
     * @throws RequestError
     * @throws ParamsError
     */
    public static function createPayment($amount, $installmentsCount)
    {
        return ClientTestHelper::getAlmaClient()->payments->create(self::paymentData($amount, $installmentsCount));
    }

    private static function paymentData($amount, $installmentsCount = 3)
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