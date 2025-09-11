<?php

namespace Alma\API\Domain\Adapter;

interface OrderAdapterInterface {

	public function __call( string $name, array $arguments );

	public function getOrderLines(): array;

	public function getPaymentId(): string;

	public function getMerchantReference(): string;

	public function getRemainingRefundAmount(): float;

	public function isFullyRefunded(): bool;

	public function isPaidWithAlma(): bool;

	public function hasATransactionId(): bool;

	public function isRefundable(): bool;

    /**
     * Get the order total in cents.
     *
     * @param string $orderId
     *
     * @return int The order total in cents.
     */
    public function getOrderTotal( string $orderId ): int;
}
