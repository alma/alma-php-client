<?php

namespace Alma\API\DTO\ShareOfCheckout;

use Alma\API\DTO\DtoInterface;

class ShareOfCheckoutTotalOrderDto implements DTOInterface
{
    private int $totalOrderCount;
    private int $totalAmount;
    private string $currency;

    public function __construct(int $totalOrderCount, int $totalAmount, string $currency) {
        $this->setTotalOrderCount($totalOrderCount);
        $this->setTotalAmount($totalAmount);
        $this->setCurrency($currency);
    }

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

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function toArray(): array {
        return [
            "total_order_count" => $this->totalOrderCount,
			"total_amount" => $this->totalAmount,
		    "currency" => $this->currency,
        ];
    }
}
