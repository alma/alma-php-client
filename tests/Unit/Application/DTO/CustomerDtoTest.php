<?php

namespace Alma\API\Tests\Unit\Application\DTO;

use Alma\API\Application\DTO\AddressDto;
use Alma\API\Application\DTO\CustomerDto;
use PHPUnit\Framework\TestCase;

class CustomerDtoTest extends TestCase
{
    public function testCustomerDto()
    {
        $data = [
            'is_business' => true,
            'business_id_number' => '1234567890',
            'business_name' => 'Doe Enterprises',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@doe.com',
            'phone' => '1234567890',
            'addresses' => [
                0 => [
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
                ],
            ],
            'created' => '2023-10-01T12:00:00Z',
            'id' => 'customer_123',
            'birth_date' => '1990-01-01',
            'banking_data_collected' => true,
        ];

        $addressDto = (new AddressDto())
            ->setFirstName($data['addresses'][0]['first_name'])
            ->setLastName($data['addresses'][0]['last_name'])
            ->setEmail($data['addresses'][0]['email'])
            ->setPhone($data['addresses'][0]['phone'])
            ->setLine1($data['addresses'][0]['line1'])
            ->setLine2($data['addresses'][0]['line2'])
            ->setPostalCode($data['addresses'][0]['postal_code'])
            ->setCity($data['addresses'][0]['city'])
            ->setCountySublocality($data['addresses'][0]['county_sublocality'])
            ->setStateProvince($data['addresses'][0]['state_province'])
            ->setCountry($data['addresses'][0]['country'])
            ->setCompany($data['addresses'][0]['company']);

        $customerDto = (new CustomerDto())
            ->setIsBusiness($data['is_business'])
            ->setBusinessIdNumber($data['business_id_number'])
            ->setBusinessName($data['business_name'])
            ->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setEmail($data['email'])
            ->setPhone($data['phone'])
            ->addAddress($addressDto)
            ->setCreated($data['created'])
            ->setId($data['id'])
            ->setBirthdate($data['birth_date'])
            ->setBankingDataCollected($data['banking_data_collected']);

        $this->assertEquals($data, $customerDto->toArray());
    }

    public function testInvalidEmail()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CustomerDto())->setEmail('invalid-email');
    }
}