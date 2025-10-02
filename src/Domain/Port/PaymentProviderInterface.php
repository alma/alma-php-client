<?php

namespace Alma\API\Domain\Port;

use Alma\API\Application\DTO\CustomerDto;
use Alma\API\Application\DTO\OrderDto;
use Alma\API\Application\DTO\PaymentDto;
use Alma\API\Application\DTO\RefundDto;
use Alma\API\Domain\Entity\Payment;

interface PaymentProviderInterface
{
    /**
     * Create a new payment.
     *
     * @param PaymentDto  $paymentDto The payment data transfer object.
     * @param OrderDto    $orderDto The order data transfer object.
     * @param CustomerDto $customerDto The customer data transfer object.
     *
     * @return Payment The created payment.
     */
    public function createPayment(PaymentDto $paymentDto, OrderDto $orderDto, CustomerDto $customerDto): Payment;

    /**
     * Fetch a payment by its ID.
     *
     * @param string $paymentId The ID of the payment to fetch.
     */
    public function fetchPayment( string $paymentId ): Payment;

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
