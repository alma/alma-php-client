<?php

namespace Alma\API\Tests\Integration\Endpoints;

use Alma\API\Entities\Order;
use Alma\API\Exceptions\AlmaException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Tests\Integration\TestHelpers\ClientTestHelper;
use Alma\API\Tests\Integration\TestHelpers\PaymentTestHelper;
use PHPUnit\Framework\TestCase;

class OrdersTest extends TestCase
{
    protected static $almaClient;
    protected static $payment;

    public static function setUpBeforeClass(): void
    {
        OrdersTest::$almaClient = ClientTestHelper::getAlmaClient();
        OrdersTest::$payment = PaymentTestHelper::createPayment(26500, 3);
    }

    public function testCanCreateANewOrder()
    {
        $payment = OrdersTest::$payment;
        $order = $payment->orders[0];
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('ABC-123', $order->getMerchantReference());
        $newOrder = OrdersTest::$almaClient->payments->addOrder($payment->id, ['merchant_reference' => 'ABC-123-NEW']);
        $this->assertInstanceOf(Order::class, $newOrder);
        $this->assertEquals('ABC-123-NEW', $newOrder->getMerchantReference());
    }

    public function testAddOrderTrackingThrowErrorWithBadData()
    {
        $this->expectException(RequestException::class);
        $payment = OrdersTest::$payment;
        $order = $payment->orders[0];
        $this->assertInstanceOf(Order::class, $order);
        OrdersTest::$almaClient->orders->addTracking($order->getExternalId(), 'UPS', null);
    }

    public function testAddOrderTracking()
    {
        $payment = OrdersTest::$payment;
        $order = $payment->orders[0];
        $this->assertInstanceOf(Order::class, $order);
        $this->assertNull(
            OrdersTest::$almaClient->orders->addTracking(
                $order->getExternalId(),
                'UPS',
                'UPS_123456',
                'https://tracking.com'
            )
        );
    }
}