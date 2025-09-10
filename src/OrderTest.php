<?php

namespace Alma\API;

use Alma\API\Entity\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{

    public function testOrderConstructWithPaymentData()
    {
        $order = new Order($this->getPaymentOrderData());
        $this->assertEquals($this->getPaymentOrderData()['comment'], $order->getComment());
        $this->assertEquals($this->getPaymentOrderData()['created'], $order->getCreatedAt());
        $this->assertEquals($this->getPaymentOrderData()['id'], $order->getExternalId());
        $this->assertEquals($this->getPaymentOrderData()['merchant_reference'], $order->getMerchantReference());
        $this->assertEquals($this->getPaymentOrderData()['payment'], $order->getPaymentId());
        $this->assertNull($order->getUpdatedAt());
    }
    public function testOrderConstructUpdateOrderData()
    {
        $order = new Order($this->getUpdateOrderData());
        $this->assertEquals($this->getUpdateOrderData()['comment'], $order->getComment());
        $this->assertEquals($this->getUpdateOrderData()['created'], $order->getCreatedAt());
        $this->assertEquals($this->getUpdateOrderData()['id'], $order->getExternalId());
        $this->assertEquals($this->getUpdateOrderData()['merchant_reference'], $order->getMerchantReference());
        $this->assertEquals($this->getUpdateOrderData()['payment'], $order->getPaymentId());
        $this->assertEquals($this->getUpdateOrderData()['updated'], $order->getUpdatedAt());
    }

    private function getPaymentOrderData(): array
    {
        return [
            "comment" => 'Created from order endpoint test',
            "created" => 1757407747,
            "customer_url" => null,
            "data" => [],
            "id" => "order_121hUbT2DLnhrK7lVhedy8R1ejqiF4NQq1",
            "merchant_reference" => "18491",
            "merchant_url" => null,
            "payment" => ""
        ];
    }
    private function getUpdateOrderData(): array
    {
        return [
            "comment" => null,
            "created" => 1757407747,
            "customer_url" => null,
            "data" => [],
            "id" => "order_121hUbT2DLnhrK7lVhedy8R1ejqiF4NQq1",
            "merchant_reference" => "18491",
            "merchant_url" => null,
            "payment" => "",
            "updated" => 1757420429
        ];
    }
}