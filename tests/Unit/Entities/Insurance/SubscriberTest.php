<?php

namespace Alma\API\Tests\Unit\Entities\Insurance;

use Alma\API\Entities\Insurance\Subscriber;
use PHPUnit\Framework\TestCase;

class SubscriberTest extends TestCase
{
    /**
     * @var Subscriber
     */
    private $subscriber;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->subscriber = $this->createNewSubscriber();
    }

    /**
     * @return void
     */
    public function testConstructObject()
    {
        $data = $this->getSubscriberData();
        $this->assertSame(Subscriber::class, get_class($this->subscriber));
        $this->assertEquals($data['email'], $this->subscriber->getEmail());
        $this->assertEquals($data['phone_number'], $this->subscriber->getPhoneNumber());
        $this->assertEquals($data['last_name'], $this->subscriber->getLastName());
        $this->assertEquals($data['first_name'], $this->subscriber->getFirstName());
        $this->assertEquals($data['birthdate'], $this->subscriber->getBirthDate());
        $this->assertEquals($data['address_line_1'], $this->subscriber->getAddressLine1());
        $this->assertEquals($data['address_line_2'], $this->subscriber->getAddressLine2());
        $this->assertEquals($data['zip_code'], $this->subscriber->getZipCode());
        $this->assertEquals($data['city'], $this->subscriber->getCity());
        $this->assertEquals($data['country'], $this->subscriber->getCountry());
    }

    /**
     * @return array
     */
    public function getSubscriberData()
    {
        return [
            'email' => "mathis.dupuy@almapay.com",
            'phone_number'=> '+33622484646',
            'last_name' => 'sub1',
            'first_name' => 'sub1',
            'birthdate' => null,
            'address_line_1' => 'adr1',
            'address_line_2' => 'adr1',
            'zip_code' => 'adr1',
            'city' => 'adr1',
            'country' => 'adr1'
        ];
    }

    /**
     * @return Subscriber
     */
    public function createNewSubscriber()
    {
        $data = $this->getSubscriberData();
        return new Subscriber(
            $data['email'],
            $data['phone_number'],
            $data['last_name'],
            $data['first_name'],
            $data['address_line_1'],
            $data['address_line_2'],
            $data['zip_code'],
            $data['city'],
            $data['country'],
            $data['birthdate']
        );
    }
}
