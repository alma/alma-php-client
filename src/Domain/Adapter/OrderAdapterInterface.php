<?php

namespace Alma\API\Domain\Adapter;

interface OrderAdapterInterface {

    /**
     * Get the lines of the order.
     * This method retrieves the items from the order and maps them to OrderLineAdapterInterface.
     *
     * @return OrderLineAdapterInterface[]
     */
    public function getOrderLines(): array;

    /**
     * Get the Payment identifier.
     * This method retrieves the transaction ID of the order, which is used to identify the payment
     *
     * @return string The transaction ID of the order.
     */
    public function getPaymentId(): string;

    /**
     * Get the merchant reference.
     * This method retrieves the order number, which serves as the merchant reference for the order.
     *
     * @return string The order number of the order.
     */
    public function getMerchantReference(): string;

    /**
     * Get the remaining refund amount.
     * This method calculates the remaining refund amount by subtracting the total refunded amount from
     * the total order amount.
     *
     * @return int The remaining refund amount in cents.
     */
    public function getRemainingRefundAmount(): int;

    /**
     * Check if the order is fully refunded.
     * This method checks if the remaining refund amount is zero, indicating that the order has been
     * fully refunded.
     *
     * @return bool True if the order is fully refunded, false otherwise.
     */
    public function isFullyRefunded(): bool;

    /**
     * Check if the order is paid with Alma payment method.
     * This method checks if the order's payment method is one of the Alma payment methods.
     *
     * @return bool True if the order is paid with Alma, false otherwise.
     */
    public function isPaidWithAlma(): bool;

    /**
     * Check if the order has a transaction ID.
     * This method checks if the order has a transaction ID set, which is necessary for processing
     * refunds and other payment-related operations.
     *
     * @return bool True if the order has a transaction ID, false otherwise.
     */
    public function hasATransactionId(): bool;

    /**
     * Check if the order is refundable.
     * This method checks if the order is paid with Alma and has a transaction ID,
     * indicating that it can be refunded.
     *
     * @return bool True if the order is refundable, false otherwise.
     */
    public function isRefundable(): bool;

    /**
     * Get the order total in cents.
     *
     * @return int The order total in cents.
     */
    public function getTotal(): int;
}
