<?php

namespace Alma\API\DTO\ShareOfCheckout;

use Alma\API\DTO\DtoInterface;

class ShareOfCheckoutPaymentMethodDto implements DTOInterface
{
    private string $paymentMethodName;
    private array $orders = [];

    /**
     * Constructor for ShareOfCheckoutPaymentMethodDto.
     * @param string $paymentMethodName
     */
    public function __construct(string $paymentMethodName)
    {
        $this->setPaymentMethodName($paymentMethodName);
    }

    /**
     * Set the payment method name.
     * @param string $paymentMethodName
     * @return $this
     */
    private function setPaymentMethodName(string $paymentMethodName): self
    {
        $this->paymentMethodName = $paymentMethodName;
        return $this;
    }

    /**
     * Add an order to the DTO.
     * @param ShareOfCheckoutOrderDto $order
     * @return $this
     */
    public function addOrder(ShareOfCheckoutOrderDto $order): self
    {
        $this->orders[] = $order->toArray();
        return $this;
    }

    /**
     * Convert the DTO to an array.
     * @return array
     */
    public function toArray(): array
    {
        return [
            "payment_method_name" => $this->paymentMethodName,
            "orders" => $this->orders,
        ];
    }
}
