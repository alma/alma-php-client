<?php

namespace Alma\API\Tests\Unit\Entity;

use Alma\API\Domain\Entity\FeePlan;
use Alma\API\Infrastructure\Exception\ParametersException;
use PHPUnit\Framework\TestCase;

class FeePlanTest extends TestCase
{
    const VALID_AMOUNT = 10000;
    const INVALID_AMOUNT = 300000;
    private ?FeePlan $AllowedFeePlan;
    private ?FeePlan $NoAllowedFeePlan;
    public function setUp(): void
    {
        $planData = [
            "available_in_pos" => true,
            "available_online" => true,
            "customer_fee_variable" => 380,
            "deferred_days" => 1,
            "deferred_months" => 2,
            "deferred_trigger_bypass_scoring" => false,
            "deferred_trigger_limit_days" => null,
            "first_installment_ratio" => null,
            "installments_count" => 3,
            "kind" => "general",
            "max_purchase_amount" => 200000,
            "merchant" => "merchant_11xYpTY1GTkww5uWFKFdOllK82S1r7j5v5",
            "merchant_fee_variable" => 12,
            "merchant_fee_fixed" => 18,
            "min_purchase_amount" => 5000
        ];

        $this->AllowedFeePlan = new FeePlan(array_merge($planData, [
            "allowed" => true,
        ]));
        $this->NoAllowedFeePlan = new FeePlan(array_merge($planData, [
            "allowed" => false,
        ]));
    }

    public function tearDown(): void
    {
        $this->AllowedFeePlan = null;
        $this->NoAllowedFeePlan = null;
    }

    public function testConstructorSetsValuesCorrectly()
    {
        $this->assertTrue( $this->AllowedFeePlan->isAllowed());
        $this->assertTrue( $this->AllowedFeePlan->isAvailableOnline());
        $this->assertEquals( 380, $this->AllowedFeePlan->getCustomerFeeVariable());
        $this->assertEquals(1, $this->AllowedFeePlan->getDeferredDays());
        $this->assertEquals(2, $this->AllowedFeePlan->getDeferredMonths());
        $this->assertEquals(3, $this->AllowedFeePlan->getInstallmentsCount());
        $this->assertEquals(FeePlan::KIND_GENERAL, $this->AllowedFeePlan->getKind());
        $this->assertEquals(12, $this->AllowedFeePlan->getMerchantFeeVariable());
        $this->assertEquals(18, $this->AllowedFeePlan->getMerchantFeeFixed());
        $this->assertEquals(200000, $this->AllowedFeePlan->getMaxPurchaseAmount());
        $this->assertEquals(5000, $this->AllowedFeePlan->getMinPurchaseAmount());
    }

    public function testEligibilityWithoutOverride()
    {
        $this->AllowedFeePlan->enable();
        $this->assertTrue($this->AllowedFeePlan->isEligible(self::VALID_AMOUNT));
        $this->assertFalse($this->AllowedFeePlan->isEligible(self::INVALID_AMOUNT));
    }

    public function testisAvailable()
    {
        // A plan that is allowed is available only when enabled
        $this->assertFalse($this->AllowedFeePlan->isAvailable());
        $this->AllowedFeePlan->enable();
        $this->assertTrue($this->AllowedFeePlan->isAvailable());

        // A plan that is not allowed is never available
        $this->assertFalse($this->NoAllowedFeePlan->isAvailable());
        $this->NoAllowedFeePlan->enable();
        $this->assertFalse($this->NoAllowedFeePlan->isAvailable());
    }
}