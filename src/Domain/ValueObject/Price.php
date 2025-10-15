<?php

namespace Alma\API\Domain\ValueObject;

use InvalidArgumentException;

class Price {

    const EUROCENTS = 'EUROCENTS';

	private int $value;
    private string $currency;

    private array $allowedCurrencies = [self::EUROCENTS]; // Extend this list as needed

	public function __construct(int $value, string $currency) {
        if ($value < 0) {
            throw new InvalidArgumentException("Price value must be non-negative.");
        }
        $this->value = $value;

        if (!in_array($currency, $this->allowedCurrencies)) {
            throw new InvalidArgumentException("Unsupported currency: $currency");
        }
        $this->currency = $currency;
	}

    public function getValue(): int {
        return $this->value;
    }

    public function getCurrency(): string {
        return $this->currency;
    }

    public function equals(Price $price): bool {
        return $this->value === $price->getValue() && $this->currency === $price->getCurrency();
    }

    public function __toString(): string {
        return sprintf('%d %s', $this->value, $this->currency);
    }
}
