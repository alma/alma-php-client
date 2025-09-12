<?php

namespace Alma\API\Tests\Unit\DTO;

use Alma\API\Application\DTO\AddressDto;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AddressDtoTest extends TestCase
{
    public function testPaymentDto()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'phone' => '1234567890',
            'line1' => '123 Main St',
            'line2' => 'Apt 4B',
            'postal_code' => '12345',
            'city' => 'Anytown',
            'county_sublocality' => 'Downtown',
            'state_province' => 'CA',
            'country' => 'US',
            'company' => 'Doe Enterprises',
        ];

        $addressDto = (new AddressDto())
            ->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setEmail($data['email'])
            ->setPhone($data['phone'])
            ->setLine1($data['line1'])
            ->setLine2($data['line2'])
            ->setPostalCode($data['postal_code'])
            ->setCity($data['city'])
            ->setCountySublocality($data['county_sublocality'])
            ->setStateProvince($data['state_province'])
            ->setCountry($data['country'])
            ->setCompany($data['company']);

        $this->assertEquals($data, $addressDto->toArray());
    }

    public function testInvalidEmail()
    {
        $this->expectException(InvalidArgumentException::class);
        (new AddressDto())->setEmail('invalid-email');
    }
}