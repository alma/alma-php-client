<?php

namespace Alma\API\Domain\Service\API;

use Alma\API\DTO\CustomerDto;
use Alma\API\DTO\OrderDto;
use Alma\API\DTO\PaymentDto;
use Alma\API\DTO\RefundDto;
use Alma\API\Entity\Payment;

interface PaymentServiceInterface
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
