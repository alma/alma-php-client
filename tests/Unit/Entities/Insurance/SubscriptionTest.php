<?php

namespace Alma\API\Tests\Unit\Entities\Insurance;

use Alma\API\Entities\Insurance\Subscriber;
use Alma\API\Entities\Insurance\Subscription;
use PHPUnit\Framework\TestCase;

class  SubscriptionTest extends TestCase
{
    /**
     * @var Subscription
     */
    private $subscription;

    public function setUp(): void
    {
        $this->subscription = $this->createNewSubscription();
    }
    public function testConstructObject()
    {
        $data = $this->getSubscriptionData();
        $this->assertSame(Subscription::class, get_class($this->subscription));
        $this->assertEquals($data['insurance_contract_id'], $this->subscription->getContractId());
        $this->assertEquals($data['cms_reference'], $this->subscription->getCmsReference());
        $this->assertEquals($data['product_price'], $this->subscription->getProductPrice());
        $this->assertEquals($this->getSubscriber(), $this->subscription->getSubscriber());
    }

    /**
     * @return Subscription
     */
    public function createNewSubscription()
    {
        $data = $this->getSubscriptionData();

        return new Subscription(
            $data['insurance_contract_id'],
            $data['amount'],
            $data['cms_reference'],
            $data['product_price'],
            $this->getSubscriber(),
            'cancelUrl'
        );
    }

    /**
     * @return array
     */
    private function getSubscriptionData()
    {
        return [
            'insurance_contract_id' => 'insurance_contract_id_123456789',
            'amount' => 1235,
            'cms_reference' => '14-35',
            'product_price' => 10012
        ];
    }

    /**
     * @return Subscriber
     */
    private function getSubscriber()
    {
        return $this->createMock(Subscriber::class);
    }
}