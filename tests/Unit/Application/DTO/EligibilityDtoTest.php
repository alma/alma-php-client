<?php

namespace Alma\API\Tests\Unit\Application\DTO;

use Alma\API\Application\DTO\AddressDto;
use Alma\API\Application\DTO\EligibilityDto;
use Alma\API\Application\DTO\EligibilityQueryDto;
use Alma\API\Application\DTO\PaymentDto;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EligibilityDtoTest extends TestCase
{
    public function testEligibilityDto()
    {
        $data = [
            'purchase_amount'      => 5000,
            'queries'              => [
                [
                    'installments_count' => 3,
                    'deferred_days' => 0,
                    'deferred_months' => 0,
                ]
            ],
            'origin'               => PaymentDto::ORIGIN_ONLINE,
            'billing_address'      => ['city' => 'Paris'],
            'shipping_address'      => ['city' => 'London'],
        ];

        $eligibilityDto = (new EligibilityDto(5000))
            ->addQuery(new EligibilityQueryDto(3))
            ->setOrigin(PaymentDto::ORIGIN_ONLINE)
            ->setBillingAddress((new AddressDto())->setCity('Paris'))
            ->setShippingAddress((new AddressDto())->setCity('London'));

        $this->assertEquals($data, $eligibilityDto->toArray());
    }

    public function testSetNegativeAmountThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Purchase amount cannot be negative.");
        new EligibilityDto(-1000);
    }

    public function testSetInvalidOriginThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid origin value.");
        $dto = new EligibilityDto(1000);
        $dto->setOrigin('invalid_origin');
    }
}
