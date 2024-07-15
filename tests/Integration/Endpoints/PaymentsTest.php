<?php

namespace Alma\API\Tests\Integration\Legacy\Endpoints;

use Alma\API\Client;
use Alma\API\DependenciesError;
use Alma\API\ParamsError;
use Alma\API\RequestError;
use PHPUnit\Framework\TestCase;

final class PaymentsTest extends TestCase
{
    protected static $almaClient;

    /**
     * @throws DependenciesError
     * @throws ParamsError
     */
    public static function setUpBeforeClass(): void
    {
        self::$almaClient = new Client(
            $_ENV['ALMA_API_KEY'],
            ['mode' => 'test', 'api_root' => $_ENV['ALMA_API_ROOT'], 'force_tls' => false]
        );
    }


    private function paymentData($amount)
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

    /**
     * @param int $amount
     * @throws RequestError
     */
    private function createPayment($amount)
    {
        return self::$almaClient->payments->create(self::paymentData($amount));
    }


    /**
     * @throws RequestError
     */
    private function checkEligibility($amount, $eligible)
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

    /**
     * @throws RequestError
     */
    public function testCanCheckEligibility()
    {
        self::checkEligibility(1, false);
        self::checkEligibility(20000, true);
        self::checkEligibility(500000, false);
    }

    /**
     * @throws RequestError
     */
    public function testCanCreateAPayment()
    {
        $payment = self::createPayment(26300);
        self::assertEquals(26300, $payment->purchase_amount);
    }

    /**
     * @throws RequestError
     */
    public function testCanFetchAPayment()
    {
        $p1 = self::createPayment(26500);
        $p2 = self::$almaClient->payments->fetch($p1->id);

        self::assertEquals($p1->id, $p2->id);
    }
}
