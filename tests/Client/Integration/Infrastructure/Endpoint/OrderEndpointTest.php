<?php

namespace Alma\Client\Tests\Integration\Application\Endpoint;

use Alma\Client\Application\Endpoint\OrderEndpoint;
use Alma\Client\Application\PaginatedResult7;
use Alma\Client\Application\PaginatedResult8;
use Alma\Client\Domain\Entity\Order;

class OrderEndpointTest extends AbstractEndpointTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->endpoint = new OrderEndpoint($this->almaClient);
    }


    public function testFetchAll(): string
    {
        /** @var PaginatedResult7 $orders */
        $orders = $this->endpoint->fetchAll(1);
        if (PHP_VERSION_ID < 80000) {
            $this->assertInstanceOf(PaginatedResult7::class, $orders);
        } else {
            $this->assertInstanceOf(PaginatedResult8::class, $orders);
        }
        return $orders->current()['id'];
    }

    /**
     * @depends testFetchAll
     */
    public function testUpdate(string $orderId): void
    {
        $order = $this->endpoint->update($orderId, [
            "comment" => "Updated order",
        ]);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($order->getComment(), "Updated order");
    }

    /**
     * @depends testFetchAll
     */
    public function testAddTracking(string $orderId): void
    {
       $this->assertNull($this->endpoint->addTracking($orderId,'carrier','trackingNumber','trackingUrl'));
    }

    /**
     * @depends testFetchAll
     */
    public function testFetchOrder(string $orderId): void
    {
        $order = $this->endpoint->fetch($orderId);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($order->getExternalId(), $orderId);
    }

}
