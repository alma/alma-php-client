<?php

namespace Alma\API\Application\DTO\ShareOfCheckout;

use Alma\API\Application\DTO\DtoInterface;
use Alma\API\Infrastructure\Exception\ParametersException;
use DateTime;

class ShareOfCheckoutDto implements DtoInterface
{
    private DateTime $startDate;
    private DateTime $endDate;
    private array $totalOrders = [];
    private array $paymentMethods = [];

    /**
     * Constructor for ShareOfCheckoutDto.
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @throws ParametersException
     */
    public function __construct(DateTime $startDate, DateTime $endDate)
    {
        if ($endDate < $startDate) {
            throw new ParametersException("End date must be after start date.");
        }

        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }

    /**
     * Set the start date.
     * @param DateTime $startDate
     * @return $this
     */
    private function setStartDate(DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Set the end date.
     * @param DateTime $endDate
     * @return $this
     */
    private function setEndDate(DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Add a total order to the DTO.
     * @param ShareOfCheckoutTotalOrderDto $totalOrder
     * @return $this
     */
    public function addOrder(ShareOfCheckoutTotalOrderDto $totalOrder): self
    {
        $this->totalOrders[] = $totalOrder->toArray();
        return $this;
    }

    /**
     * Add a payment method to the DTO.
     * @param ShareOfCheckoutPaymentMethodDto $paymentMethod
     * @return $this
     */
    public function addPaymentMethod(ShareOfCheckoutPaymentMethodDto $paymentMethod): self
    {
        $this->paymentMethods[] = $paymentMethod->toArray();
        return $this;
    }

    /**
     * Convert the DTO to an associative array.
     * @return array
     */
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
