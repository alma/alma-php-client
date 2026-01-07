<?php

namespace Alma\API\Domain\Port;

use Alma\API\Application\DTO\MerchantBusinessEvent\CartInitiatedBusinessEventDto;
use Alma\API\Application\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEventDto;

interface MerchantProviderInterface
{
    /**
     * @param CartInitiatedBusinessEventDto $cartEventData
     * @return void
     */
    public function sendCartInitiatedBusinessEvent(CartInitiatedBusinessEventDto $cartEventData): void;

    /**
     * @param OrderConfirmedBusinessEventDto $orderConfirmedBusinessEvent
     * @return void
     */
    public function sendOrderConfirmedBusinessEvent(OrderConfirmedBusinessEventDto $orderConfirmedBusinessEvent): void;
}
