<?php

namespace Alma\API\Tests\Integration\Endpoints;

use Alma\API\Entities\Order;
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

    public function testCanUpdateOrderTracking()
    {
        $payment = OrdersTest::$payment;
        $order = $payment->orders[0];
        $this->assertInstanceOf(Order::class, $order);
        $this->assertNull($order->getCarrier());
        $this->assertNull($order->getTrackingUrl());
        $this->assertNull($order->getTrackingNumber());

        $updatedOrder = OrdersTest::$almaClient->orders->updateTracking($order->getExternalId(), null,null , 'https://tracking.com');
        $this->assertInstanceOf(Order::class, $updatedOrder);
        $this->assertNull($order->getCarrier());
        $this->assertNull($updatedOrder->getTrackingNumber());
        $this->assertEquals('https://tracking.com', $updatedOrder->getTrackingUrl());

        $updatedOrder = OrdersTest::$almaClient->orders->updateTracking($order->getExternalId(), 'UPS');
        $this->assertInstanceOf(Order::class, $updatedOrder);
        $this->assertEquals('UPS', $updatedOrder->getCarrier());
        $this->assertNull($updatedOrder->getTrackingNumber());
        $this->assertEquals('https://tracking.com', $updatedOrder->getTrackingUrl());

        $updatedOrder = OrdersTest::$almaClient->orders->updateTracking($order->getExternalId(), 'LAPOSTE','123456789' , 'https://laposte.com');
        $this->assertInstanceOf(Order::class, $updatedOrder);
        $this->assertEquals('LAPOSTE', $updatedOrder->getCarrier());
        $this->assertEquals('123456789', $updatedOrder->getTrackingNumber());
        $this->assertEquals('https://laposte.com', $updatedOrder->getTrackingUrl());
    }
}