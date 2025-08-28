<?php

namespace Alma\API\DTO\ShareOfCheckout;

use Alma\API\DTO\DtoInterface;

class ShareOfCheckoutPaymentMethodDto implements DTOInterface
{
    private string $paymentMethodName;
    private array $orders = [];

    public function __construct(string $paymentMethodName)
    {
        $this->setPaymentMethodName($paymentMethodName);
    }

    public function setPaymentMethodName(string $paymentMethodName): self
    {
        $this->paymentMethodName = $paymentMethodName;
        return $this;
    }

    public function addOrder(ShareOfCheckoutOrderDto $order): self
    {
        $this->orders[] = $order->toArray();
        return $this;
    }

    public function toArray(): array
    {
        return [
            "payment_method_name" => $this->paymentMethodName,
            "orders" => $this->orders,
        ];
    }
}
