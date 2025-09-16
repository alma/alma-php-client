<?php

namespace Alma\API\Tests\Integration\Infrastructure\Endpoint;

use Alma\API\Application\DTO\CustomerDto;
use Alma\API\Application\DTO\OrderDto;
use Alma\API\Application\DTO\PaymentDto;
use Alma\API\Application\DTO\RefundDto;
use Alma\API\Domain\Entity\Order;
use Alma\API\Domain\Entity\Payment;
use Alma\API\Infrastructure\Endpoint\PaymentEndpoint;
use Alma\API\Infrastructure\Exception\Endpoint\PaymentEndpointException;

class PaymentEndpointTest extends AbstractEndpointTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->endpoint = new PaymentEndpoint($this->almaClient);
    }

    public function testCreatePayment(): string
    {
        $paymentDto = new PaymentDto(22000);
        $paymentDto->setInstallmentsCount(3)->setDeferredDays(0)->setDeferredMonths(0)->setOrigin('online');
        $orderDto = (new OrderDto())->setComment('PHP Client Integration test order')->setMerchantReference('test-php-client-integration');
        $customerDto = (new CustomerDto())->setFirstName('Test')->setLastName('Php-client Integration');

        $payment = $this->endpoint->create($paymentDto, $orderDto, $customerDto);
        $this->assertNotNull($payment);
        $this->assertInstanceOf(Payment::class, $payment);
        return $payment->getId();
    }

    /**
     * @depends testCreatePayment
     */
    public function testFetchPayment(string $paymentId): Payment
    {
        $payment = $this->endpoint->fetch($paymentId);
        $this->assertNotNull($payment);
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame($paymentId, $payment->getId());
        return $payment;
    }

    /**
     * @depends testFetchPayment
     */
    public function testPaymentProperties(Payment $payment): void
    {
        $this->assertSame([], $payment->getCustomData());
        $customData = ['payment' => ['custom_data' => ['order_id' => '12345']]];
        $editedPayment = $this->endpoint->edit($payment->getId(), $customData);
        $this->assertSame($payment->getId(), $editedPayment->getId());
        $this->assertSame($customData['payment']['custom_data'], $editedPayment->getCustomData());
    }

    /**
     * @depends testFetchPayment
     */
    public function testAddOrderPut(Payment $payment): Payment
    {
        $this->assertEquals(1, $payment->getOrders()->count());
        $this->assertSame('test-php-client-integration', $payment->getOrders()->offsetGet(0)->getMerchantReference());
        $orderData = [
                "merchant_reference" => "test-php-client-integration-2",
                "comment" => "Second order for php client integration test",
        ];
        $newOrder = $this->endpoint->addOrder($payment->getId(),$orderData);
        $this->assertInstanceOf(Order::class, $newOrder);
        $this->assertSame($orderData['merchant_reference'], $newOrder->getMerchantReference());
        $this->assertSame($orderData['comment'], $newOrder->getComment());

        $updatedPayment = $this->endpoint->fetch($payment->getId());
        $this->assertEquals(2, $updatedPayment->getOrders()->count());
        return $updatedPayment;
    }

    /**
     * @depends testAddOrderPut
     */
    public function testAddOrderPost(Payment $payment): Payment
    {
        $this->assertEquals(2, $payment->getOrders()->count());
        $orderData = [
            "merchant_reference" => "test-php-client-integration-override",
            "comment" => "Override order for php client integration test",
        ];
        $newOrder = $this->endpoint->addOrder($payment->getId(),$orderData, true);
        $this->assertInstanceOf(Order::class, $newOrder);
        $this->assertSame($orderData['merchant_reference'], $newOrder->getMerchantReference());
        $this->assertSame($orderData['comment'], $newOrder->getComment());

        $updatedPayment = $this->endpoint->fetch($payment->getId());
        $this->assertEquals(1, $updatedPayment->getOrders()->count());

        return $updatedPayment;
    }

    /**
     * @depends testAddOrderPost
     */
    public function testAddOrderStatusByMerchantRef(Payment $payment): void
    {
        $this->assertEquals(1, $payment->getOrders()->count());
        $order = $payment->getOrders()->offsetGet(0);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame('test-php-client-integration-override', $order->getMerchantReference());

        $this->assertNull($this->endpoint->addOrderStatusByMerchantOrderReference(
            $payment->getId(),
            $order->getMerchantReference(),
            'shipped',
            true
        ));
    }

    /**
     * @depends testAddOrderPost
     */
    public function testRefundErrorMessageNotStarted(Payment $payment): void
    {
        $refundDto = (new RefundDto())->setAmount(2000)->setComment('Test refund before starting payment');
        try {
            $this->endpoint->refund($payment->getId(), $refundDto);
            $this->fail('Expected PaymentEndpointException was not thrown.');
        } catch (PaymentEndpointException $e) {
            $this->assertEquals(400, $e->response->getStatusCode());
            $this->assertEquals('Bad Request', $e->response->getReasonPhrase());
            $errorContent = json_decode($e->response->getBody()->getContents(), true);
            $this->assertEquals('validation_error', $errorContent['error_code']);
            $this->assertEquals('not_started', $errorContent['errors'][0]['value']);
            return;
        }
    }

    /**
     * @depends testFetchPayment
     */
    public function testFlagAsPotentialFraud(Payment $payment): void
    {
        $this->assertNull($this->endpoint->flagAsPotentialFraud($payment->getId(), 'Test reason for flagging as fraud php client integration test'));
    }

    /**
     * @depends testFetchPayment
     */
    public function testCancelPayment(Payment $payment): void
    {
        $this->assertNull($this->endpoint->cancel($payment->getId()));
    }
}
