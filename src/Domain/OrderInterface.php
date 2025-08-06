<?php

namespace Alma\API\Domain;

interface OrderInterface {

	public function __call( string $name, array $arguments );

	public function getOrderItems(): array;

	public function getPaymentId(): string;

	public function getMerchantReference(): string;

	public function getRemainingRefundAmount(): float;

	public function isFullyRefunded(): bool;

	public function isPaidWithAlma(): bool;

	public function hasATransactionId(): bool;

	public function isRefundable(): bool;
}
