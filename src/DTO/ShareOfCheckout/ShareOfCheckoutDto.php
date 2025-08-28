<?php

namespace Alma\API\DTO\ShareOfCheckout;

use Alma\API\DTO\DtoInterface;
use Alma\API\DTO\OrderDto;

class ShareOfCheckoutDto implements DtoInterface
{
    private \DateTime $startDate;
    private \DateTime $endDate;
    private array $totalOrders = [];
    private array $paymentMethods = [];

    public function __construct(\DateTime $startDate, \DateTime $endDate)
    {
        if ($endDate < $startDate) {
            throw new \InvalidArgumentException("End date must be after start date.");
        }

        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function setStartDate(\DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function setEndDate(\DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function addOrder(ShareOfCheckoutTotalOrderDto $totalOrder): self
    {
        $this->totalOrders[] = $totalOrder->toArray();
        return $this;
    }

    public function addPaymentMethod(ShareOfCheckoutPaymentMethodDto $paymentMethod): self
    {
        $this->paymentMethods[] = $paymentMethod->toArray();
        return $this;
    }

    public function toArray(): array
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'orders' => $this->totalOrders,
            'payment_methods' => $this->paymentMethods,
        ];
    }
}
