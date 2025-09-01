<?php

namespace Alma\API\DTO\ShareOfCheckout;

use Alma\API\DTO\DtoInterface;

class ShareOfCheckoutOrderDto implements DTOInterface
{
    private int $orderCount;
    private int $amount;
    private string $currency;

    /**
     * Constructor for ShareOfCheckoutOrderDto.
     * @param int $orderCount
     * @param int $amount
     * @param string $currency
     */
    public function __construct(int $orderCount, int $amount, string $currency) {
        $this->setOrderCount($orderCount);
        $this->setAmount($amount);
        $this->setCurrency($currency);
    }

    /**
     * Set the order count.
     * @param int $orderCount
     * @return $this
     */
    public function setOrderCount(int $orderCount): self
    {
        $this->orderCount = $orderCount;
        return $this;
    }

    /**
     * Set the amount.
     * @param int $amount
     * @return $this
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Set the currency.
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Convert the DTO to an array.
     * @return array
     */
    public function toArray(): array {
        return [
            "order_count" => $this->orderCount,
			"amount" => $this->amount,
		    "currency" => $this->currency,
        ];
    }
}
