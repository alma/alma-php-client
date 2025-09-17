<?php

namespace Alma\API\Tests\Integration\Infrastructure\Endpoint;

use Alma\API\Application\DTO\MerchantBusinessEvent\CartInitiatedBusinessEventDto;
use Alma\API\Application\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEventDto;
use Alma\API\Domain\Entity\FeePlanList;
use Alma\API\Domain\Entity\Merchant;
use Alma\API\Infrastructure\Endpoint\MerchantEndpoint;

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
