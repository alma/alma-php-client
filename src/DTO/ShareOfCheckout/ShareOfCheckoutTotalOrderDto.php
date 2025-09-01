<?php

namespace Alma\API\DTO\ShareOfCheckout;

use Alma\API\DTO\DtoInterface;

class ShareOfCheckoutTotalOrderDto implements DTOInterface
{
    private int $totalOrderCount;
    private int $totalAmount;
    private string $currency;

    /**
     * Constructor for ShareOfCheckoutTotalOrderDto.
     * @param int $totalOrderCount
     * @param int $totalAmount
     * @param string $currency
     */
    public function __construct(int $totalOrderCount, int $totalAmount, string $currency)
    {
        $this->setTotalOrderCount($totalOrderCount);
        $this->setTotalAmount($totalAmount);
        $this->setCurrency($currency);
    }

    /**
     * Set the total order count.
     * @param int $totalOrderCount
     * @return $this
     */
    public function setTotalOrderCount(int $totalOrderCount): self
    {
        $this->totalOrderCount = $totalOrderCount;
        return $this;
    }

    public function setTotalAmount(int $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
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
    public function toArray(): array
    {
        return [
            "total_order_count" => $this->totalOrderCount,
            "total_amount" => $this->totalAmount,
            "currency" => $this->currency,
        ];
    }
}
