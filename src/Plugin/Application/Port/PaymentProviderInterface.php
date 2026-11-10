<?php

namespace Alma\Plugin\Application\Port;

use Alma\Client\Application\DTO\CustomerDto;
use Alma\Client\Application\DTO\OrderDto;
use Alma\Client\Application\DTO\PaymentDto;
use Alma\Client\Application\DTO\RefundDto;
use Alma\Client\Domain\Entity\Payment;

interface PaymentProviderInterface
{
    /**
     * Create a new payment.
     *
     * @param PaymentDto  $payment_dto The payment data transfer object.
     * @param OrderDto    $order_dto The order data transfer object.
     * @param CustomerDto $customer_dto The customer data transfer object.
     *
     * @return Payment The created payment.
     */
    public function createPayment(PaymentDto $payment_dto, OrderDto $order_dto, CustomerDto $customer_dto): Payment;

    /**
     * Fetch a payment by its ID.
     *
     * @param string|null $payment_id The ID of the payment to fetch.
     */
    public function fetchPayment( ?string $payment_id ): Payment;

    /**
     * Flag a payment as potential fraud.
     */
    public function flagAsFraud( string $id, string $reason ): bool;

    /**
     * Refund a payment.
     *
     * @param string    $paymentId The ID of the payment to refund.
     * @param RefundDto $refundDto The Refund Data Transfer Object containing the refund details.
     *
     * @return bool
     */
    public function refundPayment( string $paymentId, RefundDto $refundDto ): bool;
}
