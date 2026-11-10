<?php

namespace Alma\Client\Tests\Integration\Application\Endpoint;

use Alma\Client\Application\DTO\MerchantBusinessEvent\CartInitiatedBusinessEventDto;
use Alma\Client\Application\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEventDto;
use Alma\Client\Application\Endpoint\MerchantEndpoint;
use Alma\Client\Domain\Entity\FeePlanList;
use Alma\Client\Domain\Entity\Merchant;

class MerchantEndpointTest extends AbstractEndpointTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->endpoint = new MerchantEndpoint($this->almaClient);
    }

    public function testMe(): void
    {
        $merchant = $this->endpoint->me();
        $this->assertInstanceOf(Merchant::class, $merchant);
    }

    public function testGetFeePlanList(): void
    {
        $feePlanList = $this->endpoint->getFeePlanList();
        $this->assertInstanceOf(FeePlanList::class, $feePlanList);
    }

    public function testSendCartInitiatesBusinessEvent(): void
    {
        $this->assertNull(
            $this->endpoint->sendCartInitiatedBusinessEvent(
                new CartInitiatedBusinessEventDto("cart_id")
            )
        );
    }

    public function testSendOrderConfirmedBusinessEvent(): void
    {
        $this->assertNull(
            $this->endpoint->sendOrderConfirmedBusinessEvent(
                new OrderConfirmedBusinessEventDto(true, false, true, "order_id", "cart_id", "payment_id")
            )
        );
    }
}
