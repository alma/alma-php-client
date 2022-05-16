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
                    'first_name' => 'Alma-php-client',
                    'last_name' => 'integration tests',
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

    public function createEligibilityV1Payload($amount, $installments) {
        return [
            'payment' => [
                'purchase_amount' => $amount,
                'installments_count' => $installments,
                'shipping_address' => [
                    'first_name' => 'Alma-php-client',
                    'last_name' => 'integration tests',
                    'line1' => '2 rue de la rue',
                    'city' => 'Paris',
                    'postal_code' => '75002',
                    'country' => 'FR',
                ]
            ]
        ];
    }

    public function eligibilityV1Data() {
        return [
            [ $this->createEligibilityV1Payload(1, [2,3]), false ],
            [ $this->createEligibilityV1Payload(20000, [3]), true ],
            [ $this->createEligibilityV1Payload(500000, [3]), false ],
        ];
    }

    /**
     * Test the fullRefund method with valid datas
     * @dataProvider eligibilityV1Data
     * @return void
     */
    public function testEligibilityV1($data, $eligible)
    {
        $eligibilities = self::$almaClient->payments->eligibility($data);

        foreach($eligibilities as $eligibility) {
            self::assertEquals($eligible, $eligibility->isEligible);
            if (!$eligible) {
                self::assertArrayHasKey('purchase_amount', $eligibility->reasons);
                self::assertEquals('invalid_value', $eligibility->reasons['purchase_amount']);

                self::assertArrayHasKey('purchase_amount', $eligibility->constraints);
                self::assertArrayHasKey('minimum', $eligibility->constraints['purchase_amount']);
                self::assertArrayHasKey('maximum', $eligibility->constraints['purchase_amount']);
            }
        }
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