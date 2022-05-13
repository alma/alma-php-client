<?php

namespace Alma\API\Tests\Integration;

use Alma\API\Client;
use Alma\API\Entities\Instalment;
use Alma\API\Entities\Payment;
use PHPUnit\Framework\TestCase;

final class PaymentsTest extends TestCase
{
    protected static $almaClient;

    public static function setUpBeforeClass()
    {
        self::$almaClient = new Client(
            $_ENV['ALMA_API_KEY'],
            ['mode' => 'test', 'api_root' => $_ENV['ALMA_API_ROOT'], 'force_tls' => false]
        );
    }


    private static function paymentData($amount)
    {
        return [
            'payment' => [
                'purchase_amount' => $amount,
                'shipping_address' => [
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'line1' => '2 rue de la rue',
                    'city' => 'Paris',
                    'postal_code' => '75002',
                    'country' => 'FR',
                ]
            ]
        ];
    }

    private static function createPayment($amount)
    {
        return self::$almaClient->payments->create(self::paymentData($amount));
    }


    private static function _testEligibility($amount, $eligible)
    {
        $eligibility = self::$almaClient->payments->eligibility(self::paymentData($amount));
        self::assertEquals($eligible, $eligibility->isEligible);

        if (!$eligible) {
            self::assertArrayHasKey('purchase_amount', $eligibility->reasons);
            self::assertEquals('invalid_value', $eligibility->reasons['purchase_amount']);

            self::assertArrayHasKey('purchase_amount', $eligibility->constraints);
            self::assertArrayHasKey('minimum', $eligibility->constraints['purchase_amount']);
            self::assertArrayHasKey('maximum', $eligibility->constraints['purchase_amount']);
        }
    }

    public static function testCanCheckEligibility()
    {
        self::_testEligibility(1, false);
        self::_testEligibility(20000, true);
        self::_testEligibility(500000, false);
    }

    public function testCanCreateAPayment()
    {
        $payment = self::createPayment(26300);
        self::assertEquals(26300, $payment->purchase_amount);
    }

    public static function testCanFetchAPayment()
    {
        $p1 = self::createPayment(26300);
        $p2 = self::$almaClient->payments->fetch($p1->id);

        self::assertEquals($p1->id, $p2->id);
    }
}