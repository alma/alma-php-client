<?php

namespace Unit\Entities;

use Alma\API\Entities\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{

    public function testOrderGetters()
    {
        $orderData = $this->orderDataFactory();
        $order = new Order($orderData);

        $this->assertEquals($orderData['payment'], $order->payment);
        $this->assertEquals($orderData['payment'], $order->getPaymentId());
        $this->assertEquals($orderData['merchant_reference'], $order->merchant_reference);
        $this->assertEquals($orderData['merchant_reference'], $order->getMerchantReference());
        $this->assertEquals($orderData['merchant_url'], $order->getMerchantUrl());
        $this->assertEquals($orderData['merchant_url'], $order->merchant_url );
        $this->assertEquals($orderData['data'], $order->data);
        $this->assertEquals($orderData['data'], $order->getOrderData());
        $this->assertEquals($orderData['comment'], $order->getComment());
        $this->assertEquals($orderData['created'], $order->getCreatedAt());
        $this->assertEquals($orderData['customer_url'], $order->getCustomerUrl());
        $this->assertEquals($orderData['id'], $order->id);
        $this->assertEquals($orderData['id'], $order->getExternalId());
        $this->assertEquals($orderData['updated'], $order->getUpdatedAt());
    }


    public static function orderDataFactory(
        $comment = 'my comment',
        $created = 1715331839,
        $customer_url = 'http://customer.url',
        $data = ['key' => 'value'],
        $id = 'order_123',
        $merchant_reference = 'my reference',
        $merchant_url = 'https://merchant.url',
        $payment = 'payment_123456',
        $updated = 1715331845
    )
    {
        return [
            'comment' => $comment,
            'created' => $created,
            'customer_url' => $customer_url,
            'data' => $data,
            'id' => $id,
            'merchant_reference' => $merchant_reference,
            'merchant_url' => $merchant_url,
            'payment' => $payment,
            'updated' => $updated
        ];
    }
}